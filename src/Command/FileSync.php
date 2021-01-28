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

use Laemmi\SyncTools\Helper\Config;
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = Config::parseFile();

        foreach ($config['server'] as $server) {
            $server['dest']['path_data'] = realpath($server['dest']['path_data']);

            if (!$server['dest']['path_data']) {
                throw new \InvalidArgumentException('dest:path_data not exists');
            }

            $output->writeln([
                sprintf('File sync from %s > %s', $server['src']['ssh_path'], $server['dest']['path_data'])
            ]);

            $service = new Rsync(
                $server['src']['ssh_path'] . '/',
                $server['dest']['path_data']
            );

            $service->setSshConnection($server['src']['ssh_user'], $server['src']['ssh_host']);

            foreach ($server['attributes']['rsync'] as $attribute) {
                $service->addAttribute($attribute);
            }

            $service->execute();
        }

        return 0;
    }
}
