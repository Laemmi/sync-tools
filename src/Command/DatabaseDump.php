<?php

/**
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package    sync-tools
 * @author     Michael Lämmlein <laemmi@spacerabbit.de>
 * @copyright  ©2021 Spacerabbit
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.0
 * @since      27.01.21
 */

declare(strict_types=1);

namespace Laemmi\SyncTools\Command;

use Laemmi\SyncTools\Config;
use Laemmi\SyncTools\Service\DatabaseDump\Mysql;
use Laemmi\SyncTools\Service\FileSync\Rsync;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDump extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'database:dump';

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var Mysql
     */
    protected Mysql $service;

    /**
     * @var Rsync
     */
    protected Rsync $rsync_service;

    /**
     * DatabaseDump constructor.
     * @param Config $config
     * @param Mysql $service
     * @param Rsync $rsync_service
     */
    public function __construct(
        Config $config,
        Mysql $service,
        Rsync $rsync_service
    ) {
        parent::__construct();

        $this->config = $config;
        $this->service = $service;
        $this->rsync_service = $rsync_service;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var Config\DatabaseItem $db
         */
        foreach ($this->config->databases as $db) {
            $tmpfile = tempnam(sys_get_temp_dir(), 'sync-tools-');
            $output->write('<info>' . sprintf(
                '🤘 Dump Mysql Database %s > %s from server %s',
                $db->src_db_dbname,
                $db->src_db_dump,
                $this->config->src_ssh_host,
            ) . '</info>', true);

            $service = $this->service;

            $service->setDebug($this->config->debug);

            $service->setHost($db->src_db_host);
            $service->setUser($db->src_db_user);
            $service->setPassword($db->src_db_pw);
            $service->setDatabase($db->src_db_dbname);
            $service->setPort($db->src_db_port);

            if ($this->config->ssh_force_transfer) {
                $service->setPath($db->src_db_dump);
            } else {
                $service->setPath($tmpfile);
            }
            if ($this->config->src_ssh_host) {
                $service->setSsh(
                    $this->config->src_ssh_user,
                    $this->config->src_ssh_host,
                    $this->config->src_ssh_port,
                    $this->config->src_ssh_identity
                );
                $service->setSshForceTransfer($this->config->ssh_force_transfer);
            }

            foreach ($db->attributes_mysqldump as $attribute) {
                $service->addAttribute($attribute);
            }

            $output->write('<comment>' . $service->execute() . '</comment>', true);

            if (!$this->config->ssh_force_transfer) {
                $output->write('<info>' . sprintf(
                    '🤘 Transfer Dump %s:%s > %s',
                    $this->config->src_ssh_host,
                    $tmpfile,
                    $db->src_db_dump,
                ) . '</info>', true);

                $this->rsync_service->setDebug($this->config->debug);
                $this->rsync_service->setSrcSshPath($tmpfile);
                $this->rsync_service->setSrcSshUser($this->config->src_ssh_user);
                $this->rsync_service->setSrcSshHost($this->config->src_ssh_host);
                $this->rsync_service->setSrcSshPort($this->config->src_ssh_port);
                $this->rsync_service->setSrcSshIdentity($this->config->src_ssh_identity);
                $this->rsync_service->setDestPath($db->src_db_dump);
                $this->rsync_service->addAttribute('--remove-source-files');

                $output->write('<comment>' . $this->rsync_service->execute() . '</comment>', true);
            }
        }

        return 0;
    }
}
