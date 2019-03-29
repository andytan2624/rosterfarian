<?php

namespace App\Http\Controllers;

use App\Location;
use App\User;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index() {

      $roster = [
        1 => [
          'mon' => [

          ],
          'tue' => [

          ],
          'wed' => [

          ],
          'thur' => [

          ],
          'fri' => [

          ],
        ],
        2 => [
          'mon' => [

          ],
          'tue' => [

          ],
          'wed' => [

          ],
          'thur' => [

          ],
          'fri' => [

          ],
        ]

      ];

      $day_off_roster = [
        1 => [
          'mon' => false,
          'tue' => false,
          'wed' => false,
          'thur' => false,
          'fri' => false,
        ],
        2 => [
          'mon' => false,
          'tue' => false,
          'wed' => false,
          'thur' => false,
          'fri' => false,
        ]

      ];

      // Go through each location and add it to the

      $users = User::with('dayPreferences', 'locationPreferences')->where('password', '=', '')->get()->toArray();

      $maxDays = 9;
      $userTracker = [];
      $day_off_users = [];
      // List of users who don't go on the waiting list
      $no_other_list = [];

      foreach ($users as $user) {
        foreach ($user['location_preferences'] as $location_preference) {
          if ($location_preference['location_id'] == 6 && $location_preference['not_work']) {
            $no_other_list[] = $user['id'];
          }
        }
      }

      foreach ($users as $user) {
        $userTracker[$user['id']] = [
          'id' => $user['id'],
          'name' => $user['name'],
          'workingDays' => 0,
          'daysWorked'=> [],
          'otherWaitingList' => $user['location_preferences'][0],
          'dayPreferences' => $user['day_preferences'][0],
        ];

        if ($user['day_preferences'][0]['days_off']) {
          $day_off_users[] = $user;
        }
      }

      shuffle($day_off_users);

      $locations = Location::get(['id','location_name','number_positions','automatic_sorted'])->toArray();
      $location_list = $locations;
      $day_combinations = [];

      foreach ($roster as $weekIndex => $week) {
        foreach ($week as $dayIndex => $day) {

          // add week and day to combination
          $day_combinations[] = [
            'week' => $weekIndex,
            'day' => $dayIndex,
          ];
        }
      }

      shuffle($day_combinations);
        foreach ($day_combinations as $index => $data) {
          // Now assign a user
          if (count($day_off_users) > 0) {
            $pop_user = array_shift($day_off_users);
            $day_off_roster[$data['week']][$data['day']] = $pop_user['name'];
          }

        }


      foreach ($locations as $location) {
        foreach ($roster as $weekIndex => $week) {
          foreach ($week as $dayIndex => $day) {

            $location['positions_taken'] = 0;
            $roster[$weekIndex][$dayIndex][$location['id']] = [
              'must_work' => [

              ],
              'exclusive' => [

              ],
              'non_exclusive' => [

              ],
              'positions' => $location['number_positions'],
              'nominations' => [

              ]
            ];
            // Now lets get the right people for each group
            foreach ($users as $user) {
              $can_work = false;
              if (isset($user['day_preferences']) && $user['day_preferences'][0][$dayIndex]) {
                $can_work = true;
              }
              if ($can_work && isset($user['location_preferences'])) {
                foreach ($user['location_preferences'] as $location_preference) {
                  if ($location_preference['location_id'] == $location['id']) {
                    if ($location_preference['must_work']) {
                      $roster[$weekIndex][$dayIndex][$location['id']]['must_work'][] = $user;
                      $can_work = false;
                    }

                    if ($can_work && $location_preference['exclusive']) {
                      $roster[$weekIndex][$dayIndex][$location['id']]['exclusive'][] = $user;
                      $can_work = false;
                    }

                    // if the user can not work in this particular location, set it so
                    if ($location_preference['not_work']) {
                      $can_work = false;
                    }
                    break;
                  }
                }
              }
              // If the user doesn't have any specific preference to the location, add them to the list
              if ($can_work) {
                $roster[$weekIndex][$dayIndex][$location['id']]['non_exclusive'][] = $user;
              }
            }


          }
        }
      }

      shuffle($day_combinations);

      foreach ($day_combinations as $combination) {

        shuffle($locations);

        foreach ($locations as $location) {
          if ($location['automatic_sorted']) {
            $mustworkRoster = $roster[$combination['week']][$combination['day']][$location['id']]['must_work'];
            $exclusiveRoster = $roster[$combination['week']][$combination['day']][$location['id']]['exclusive'];
            $non_exclusiveRoster = $roster[$combination['week']][$combination['day']][$location['id']]['non_exclusive'];

            shuffle($mustworkRoster);
            shuffle($exclusiveRoster);
            shuffle($non_exclusiveRoster);

            for ($i = 0; $i < $location['number_positions']; $i++) {
              $exclusiveFound = false;
              if (count($mustworkRoster) > 0) {
                foreach ($mustworkRoster as $index => $person) {

                  unset($mustworkRoster[$index]);
                  if ($userTracker[$person['id']]['workingDays'] < $maxDays
                    && $day_off_roster[$combination['week']][$combination['day']] != $person['name']
                    && !isset($userTracker[$person['id']]['daysWorked'][$combination['week'] . '|' . $combination['day']])) {
                    $roster[$combination['week']][$combination['day']][$location['id']]['nominations'][] = $person['name'];
                    $userTracker[$person['id']]['workingDays']++;
                    $userTracker[$person['id']]['daysWorked'][$combination['week'] . '|' . $combination['day']] = true;

                    $exclusiveFound = true;
                    break;
                  }

                }
              }

              if (count($exclusiveRoster) > 0 && !$exclusiveFound) {
                foreach ($exclusiveRoster as $index => $person) {

                  unset($exclusiveRoster[$index]);
                  if ($userTracker[$person['id']]['workingDays'] < $maxDays
                    && $day_off_roster[$combination['week']][$combination['day']] != $person['name']
                    && !isset($userTracker[$person['id']]['daysWorked'][$combination['week'] . '|' . $combination['day']])) {
                    $roster[$combination['week']][$combination['day']][$location['id']]['nominations'][] = $person['name'];
                    $userTracker[$person['id']]['workingDays']++;
                    $userTracker[$person['id']]['daysWorked'][$combination['week'] . '|' . $combination['day']] = true;

                    $exclusiveFound = true;
                    break;
                  }

                }
              }

              if (count($non_exclusiveRoster) > 0 && !$exclusiveFound) {
                foreach ($non_exclusiveRoster as $index => $person) {

                  unset($non_exclusiveRoster[$index]);
                  if ($userTracker[$person['id']]['workingDays'] < $maxDays
                    && $day_off_roster[$combination['week']][$combination['day']] != $person['name']
                    && !isset($userTracker[$person['id']]['daysWorked'][$combination['week'] . '|' . $combination['day']])
                  ) {
                    $roster[$combination['week']][$combination['day']][$location['id']]['nominations'][] = $person['name'];
                    $userTracker[$person['id']]['workingDays']++;
                    $userTracker[$person['id']]['daysWorked'][$combination['week'] . '|' . $combination['day']] = true;

                    break;
                  }
                }
              }
            }
            $roster[$combination['week']][$combination['day']][$location['id']]['exclusive'] = $exclusiveRoster;
            $roster[$combination['week']][$combination['day']][$location['id']]['non_exclusive'] = $non_exclusiveRoster;

          }
        }


      }

      $users_with_spots = [];
      foreach ($userTracker as $person_id => $person_data) {
        if ($person_data['workingDays'] < $maxDays && !in_array($person_id, $no_other_list)) {
          $users_with_spots[] = $person_data;
        }
      }

      while (count($users_with_spots) > 0) {
        shuffle($users_with_spots);

        $user = array_shift($users_with_spots);

        foreach ($day_combinations as $combination) {
          // add the user to the other roster sheet if they havent worked on that day yet
          if (!isset($user['daysWorked'][$combination['week']. '|' . $combination['day']])
          && count($roster[$combination['week']][$combination['day']][6]['nominations']) == 0
            && $day_off_roster[$combination['week']][$combination['day']] != $user['name']
            && $user['dayPreferences'][$combination['day']]
          ) {
            $roster[$combination['week']][$combination['day']][6]['nominations'][] = $user['name'];
            $user['workingDays']++;
            $user['daysWorked'][$combination['week']. '|' . $combination['day']] = true;
            if ($user['workingDays'] < $maxDays) {
              array_push($users_with_spots, $user);
            }
            break;
          }
          $combination = array_shift($day_combinations);
          array_push($day_combinations, $combination);
        }

      }

      // Then calculate the users who don't have 9 spots, and start filling the other 'Other spots' that need loving

      // Then print it out
//      var_dump($roster[1]['fri'][1]);
//      var_dump($userTracker);
//      die('westlife');

      return view('index', compact('roster', 'location_list', 'day_off_roster'));
    }
}
