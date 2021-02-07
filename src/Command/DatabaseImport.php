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
use Laemmi\SyncTools\Service\DatabaseImport\Mysql;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseImport extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'database:import';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = Config::parseFile();

        foreach ($config['databases'] as $db) {
            $service = new Mysql(
                $db['dest']['db_host'],
                $db['dest']['db_user'],
                $db['dest']['db_pw'],
                $db['dest']['db_dbname'],
                isset($db['dest']['db_port']) ? $db['dest']['db_port'] : 3306
            );

            if (is_file($db['src']['db_dump'])) {
                $output->write(sprintf(
                    'Import Mysql Database %s < %s',
                    $db['dest']['db_dbname'],
                    $db['src']['db_dump']
                ), true);

                $service->setPath($db['src']['db_dump']);
                $service->execute();
            }

            $db['dest']['additional_dump'] = realpath($db['dest']['additional_dump']);
            if ($db['dest']['additional_dump']) {
                $output->write(sprintf(
                    'Additional dump Mysql Database %s < %s',
                    $db['dest']['db_dbname'],
                    $db['dest']['additional_dump']
                ), true);

                $service->setPath($db['dest']['additional_dump']);
                $service->execute();
            }
        }

        return 0;
    }
}
