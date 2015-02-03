<?php
namespace Modules\Stations\Controllers;

use Entity\Station;

class BaseController extends \DF\Phalcon\Controller
{
    /*
     * @var array All available stations.
     */
    protected $stations;

    /**
     * @var Station The current active station.
     */
    protected $station;

    protected function preDispatch()
    {
        parent::preDispatch();

        $this->forceSecure();

        $user = $this->auth->getLoggedInUser();

        // Compile list of visible stations.
        $all_stations = Station::fetchAll();
        $stations = array();

        foreach($all_stations as $station)
        {
            if ($station->canManage($user))
                $stations[$station->id] = $station;
        }

        $this->stations = $stations;
        $this->view->stations = $stations;

        // Assign a station if one is selected.
        if ($this->hasParam('station'))
        {
            $station_id = (int)$this->getParam('station');
            if (isset($stations[$station_id]))
            {
                $this->station = $stations[$station_id];
                $this->view->station = $this->station;
            }
            else
            {
                throw new \DF\Exception\PermissionDenied;
            }
        }
        else if (count($this->stations) == 1)
        {
            // Convenience auto-redirect for single-station admins.
            $this->redirectFromHere(array('station' => key($this->stations)));
            return false;
        }

        // Force a redirect to the "Select" page if no station ID is specified.
        if (!$this->station && $this->dispatcher->getActionName() != 'select')
        {
            $this->redirectToRoute(array('module' => 'stations', 'controller' => 'index', 'action' => 'select', 'station' => NULL));
            return false;
        }
    }

    protected function permissions()
    {
        return $this->acl->isAllowed('is logged in');
    }
}