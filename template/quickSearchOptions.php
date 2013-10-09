<?php

/**
 *
 * @title         quickSearchOptions.php
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_quickSearchOptions">

    <table class="form-table">
        <tr>
            <td><label>Title:</label></td>
            <td><input id="<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>" value="<?php echo $title; ?>" type="text" /></td>
        </tr>
    </table>

</div>
