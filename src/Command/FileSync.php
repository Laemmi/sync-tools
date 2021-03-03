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
 * @since      28.01.21
 */

declare(strict_types=1);

namespace Laemmi\SyncTools\Command;

use Laemmi\SyncTools\Config;
use Laemmi\SyncTools\Service\FileSync\Rsync;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileSync extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'file:sync';

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var
     */
    protected Rsync $service;

    /**
     * DatabaseImport constructor.
     * @param Config $config
     * @param Rsync $service
     */
    public function __construct(Config $config, Rsync $service)
    {
        parent::__construct();

        $this->config = $config;
        $this->service = $service;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(sprintf(
            '<info>🤘 File sync from %s > %s</info>',
            $this->config->src_ssh_path,
            $this->config->dest_path
        ), true);

        $this->service->setDebug($this->config->debug);

        $this->service->setSrcSshPath($this->config->src_ssh_path);
        $this->service->setSrcSshUser($this->config->src_ssh_user);
        $this->service->setSrcSshHost($this->config->src_ssh_host);
        $this->service->setSrcSshPort($this->config->src_ssh_port);
        $this->service->setDestPath($this->config->dest_path);

        foreach ($this->config->attributes_rsync as $attribute) {
            $this->service->addAttribute($attribute);
        }

        $output->write('<comment>' . $this->service->execute() . '</comment>', true);

        return 0;
    }
}
