<?php
/**
 -------------------------------------------------------------------------

 LICENSE

 This file is part of Behaviors plugin for GLPI.

 Behaviors is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Behaviors is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with Behaviors. If not, see <http://www.gnu.org/licenses/>.

 @package   behaviors
 @author    Nelly Mahu-Lasson
 @copyright Copyright (c) 2022 Behaviors plugin team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://forge.glpi-project.org/projects/behaviors
 @link      http://www.glpi-project.org/
 @since     2010

 --------------------------------------------------------------------------
*/

class PluginBehaviorsProjectTask
{


   static function beforeUpdate(ProjectTask $task)
   {

      if (!is_array($task->input) || !count($task->input)) {
         // Already cancel by another plugin
         return false;
      }



      // Check is the connected user is a tech
      if (
         !is_numeric(Session::getLoginUserID(false))
         || (!Session::haveRightsOr('project', [Project::READALL, Project::READMY])
            || !Session::haveRight('projecttask', ProjectTask::READMY))
      ) {
         return false; // No check
      }

      if ($task->fields['auto_percent_done'] == 1 && $task->fields['is_milestone'] == 1 && $task->input['percent_done'] == 100) {
         $task->input['projectstates_id'] = self::getFinishedState();

      }

   }

   public static function getFinishedState(): string
   {
      global $DB;
      $result = "";
      $iterator = $DB->request(
         [
            'SELECT' => ['id'],
            'FROM' => ProjectState::getTable(),
            'WHERE' => [
               'is_finished' => 1
            ],
         ]
      );

      if ($iterator->count()) {
         $result = $iterator->current()['id'];
      }

      return $result;
   }
}
