<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $condition = null;
    protected $notif = null;

    public function activity()
    {
        $act = Activity::where("actor_id", Auth::id())
            ->where("is_read", false)
            ->limit(5)
            ->orderBy("id", "DEsC")
            ->get();
        $count = Activity::where("actor_id", Auth::id())
            ->where("is_read", false)
            ->count();

        $activity = [$act, $count];
        
        return $activity;
    }

    public function recordCheck($record, $database, $column, $check_data)
    {
        $bool = true;
        $instance = new $database();
        $rec = $instance::where($column, "=", $record)->get(); // RECORD
        if (sizeof($rec) > 1) {
            // CEK RECORD ADA ATAU TIDAK
            foreach ($rec as $r) {
                if (strcmp($r[$column], $check_data) == 0) {
                    $bool = true;
                }
                // JIKA RECORD SAMA MAKA BOOL AKAN TRUE
                else {
                    $bool = false;
                    return $bool; // STOP ITERATION B'CAUSE FOUND DOUBLE REC.
                }
            }
        }

        return $bool;
    }

    public function storeActivity($type, $activity)
    {
        $token = "";
        switch ($type) {
            case "Store":
                $token = "ST" . random_int(10000, 999999);
                $type = "Create";
                break;
            case "Update":
                $token = "UP" . random_int(10000, 999999);
                break;
            case "Delete":
                $token = "DT" . random_int(10000, 999999);
                break;
        }

        Activity::create([
            "actor_id" => Auth::id(),
            "token" => $token,
            "message" => $activity,
            "type" => $type,
            "created_at" => now(),
            "updated_at" => now()
        ]);

        return;
    }

    public function toastNotification($cond, $notif)
    {
        $this->condition = $cond;
        $this->notif = $notif;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function getNotif()
    {
        return $this->notif;
    }

    public function authCheck($role) 
    {
        $result = true;
        switch ($role) {
            case 'Harvesting':
                
                break;
            case 'Hauling':
            
                break;
            case 'Measurement':
            
                break;
            default:
                
                break;
        }
        if($role != 'Super Admin' || $role != 'Harvesting') 
        {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }    
    }
}
