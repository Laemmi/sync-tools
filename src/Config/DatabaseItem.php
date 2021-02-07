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
 * @author     Michael Lämmlein <michael.laemmlein@liebscher-bracht.com>
 * @copyright  ©2021 Liebscher & Bracht
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.0
 * @since      07.02.21
 */

declare(strict_types=1);

namespace Laemmi\SyncTools\Config;

class DatabaseItem
{
    /**
     * @var array
     */
    public array $attributes_mysqldump;

    /**
     * @var string
     */
    public string $src_db_host = 'localhost';

    /**
     * @var int
     */
    public int $src_db_port = 3306;

    /**
     * @var string
     */
    public string $src_db_user;

    /**
     * @var string
     */
    public string $src_db_pw;

    /**
     * @var string
     */
    public string $src_db_dbname;

    /**
     * @var string
     */
    public string $src_db_dump;

    /**
     * @var string
     */
    public string $dest_db_host = 'localhost';

    /**
     * @var int
     */
    public int $dest_db_port = 3306;

    /**
     * @var string
     */
    public string $dest_db_user;

    /**
     * @var string
     */
    public string $dest_db_pw;

    /**
     * @var string
     */
    public string $dest_db_dbname;
}
