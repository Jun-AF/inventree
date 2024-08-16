<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class RktController extends Controller
{
    protected $database;
    protected $reference;

    public function __construct(Database $database) {
        $this->middleware('auth');
        $this->database = $database;
        $this->reference = $this->database->getReference('rkt');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "Planner") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }
        
        $activities = $this->activity();
        $rkt = $this->reference->getValue();

        return view('rkt.index', compact('activities','rkt'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "Planner") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $validator = $request->validateWithBag([
            'no_petak' => ['required', 'string', 'max:10'],
            'luasan' => ['required', 'string', 'max:7'],
            'jenis_tanaman' => ['required', 'string'],
            'tanggal_tanam' => ['required', 'date'],
            'kontraktor_tanam' => ['required', 'string', 'max:30'],
            'jarak_tanam' => ['required', 'string']
        ]);

        $key = '';
        $rkt = '';

        try {
            $rkt = [
                'no_petak' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),
                'luasan' => filter_input(INPUT_POST, $request->luasan, FILTER_SANITIZE_STRING),
                'jenis_tanaman' => filter_input(INPUT_POST, $request->jenis_tanaman, FILTER_SANITIZE_STRING),
                'tanggal_tanam' => filter_input(INPUT_POST, $request->tanggal_tanam, FILTER_SANITIZE_STRING),
                'kontraktor_tanam' => filter_input(INPUT_POST, $request->kontraktor_tanam, FILTER_SANITIZE_STRING),
                'jarak_tanam' => filter_input(INPUT_POST, $request->jarak_tanam, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->reference->set($rkt);
            $key = $this->reference->limitToLast(1)->getSnapshot();
        } catch (FirebaseException $e) {
            $this->toastNotification("Fails", "Failed in storing record");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $this->toastNotification(
            "Success",
            "You have just created an asset record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A rkt has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "no_petak : " . $rkt['no_petak'] . "<br>".
        "tanggal_tanam : " . $rkt['tanggal_tanam'] . "<br>".
        "kontraktor_tanam : " . $rkt['kontraktor_tanam'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('rkt')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "Planner") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $validator = $request->validateWithBag([
            'key' => ['required', 'string'],
            'no_petak' => ['required', 'string', 'max:10'],
            'luasan' => ['required', 'string', 'max:7'],
            'jenis_tanaman' => ['required', 'string'],
            'tanggal_tanam' => ['required', 'date'],
            'kontraktor_tanam' => ['required', 'string', 'max:30'],
            'jarak_tanam' => ['required', 'string']
        ]);

        $rkt = '';

        try {
            $rkt = $this->database->getReference('rkt/'.$request->key)->getValue();
            $rkt['no_petak'] = filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING);
            $rkt['luasan'] = filter_input(INPUT_POST, $request->luasan, FILTER_SANITIZE_STRING);
            $rkt['jenis_tanaman'] = filter_input(INPUT_POST, $request->jenis_tanaman, FILTER_SANITIZE_STRING);
            $rkt['tanggal_tanam'] = filter_input(INPUT_POST, $request->tanggal_tanam, FILTER_SANITIZE_STRING);
            $rkt['kontraktor_tanam'] = filter_input(INPUT_POST, $request->kontraktor_tanam, FILTER_SANITIZE_STRING);
            $rkt['jarak_tanam'] = filter_input(INPUT_POST, $request->jarak_tanam, FILTER_SANITIZE_STRING);
            $rkt['updated_at'] = now();

            $this->database->getReference('rkt/'.$request->key)->update($rkt);
        } catch (FirebaseException $e) {
            $this->toastNotification("Fails", "Failed in updating record");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $this->toastNotification(
            "Success",
            "You have just updated an asset record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A rkt has just updated with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $rkt['no_petak'] . "<br>".
        "tanggal_tanam : " . $rkt['tanggal_tanam'] . "<br>".
        "kontraktor_tanam : " . $rkt['kontraktor_tanam'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('rkt')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "Planner") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $validator = $request->validate([
            'key' => ['required', 'string']
        ]);

        try {
            $rkt = $this->database->getReference('rkt/'.$request->key)->getValue();
            $this->database->getReference('rkt/'.$request->key)->remove();
        } catch (FirebaseException $e) {
            $this->toastNotification("Fails", "Failed in deleting record");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $this->toastNotification(
            "Success",
            "You have just deleted an asset record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A rkt has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $rkt['no_petak'] . "<br>".
        "tanggal_tanam : " . $rkt['tanggal_tanam'] . "<br>".
        "kontraktor_tanam : " . $rkt['kontraktor_tanam'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('rkt')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    public function truncate() 
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "Planner") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }
        
        try {
            $this->database->getReference('rkt')->set(null);
        } catch (FirebaseException $e) {
            $this->toastNotification("Fails", "Failed in deleting record");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $this->toastNotification(
            "Success",
            "You have just deleted rkt record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Rkt has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('rkt')
            ->with(["condition" => $condition, "notif" => $notif]);
    }
}
