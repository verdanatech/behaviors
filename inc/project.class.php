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

class PluginBehaviorsProject
{


   static function beforeUpdate(Project $project)
   {

      if (!is_array($project->input) || !count($project->input)) {
         return false;
      }



      // Check is the connected user is a tech
      if (
         !is_numeric(Session::getLoginUserID(false))
         || (!Session::haveRightsOr('project', [Project::READALL, Project::READMY]))
      ) {
         return false; // No check
      }
      if ($project->fields['auto_percent_done'] == 1 && $project->input['percent_done'] == 100) {
         $project->input['projectstates_id'] = self::getFinishedState();

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
