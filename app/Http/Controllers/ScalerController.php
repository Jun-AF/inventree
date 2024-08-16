<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class ScalerController extends Controller
{
    protected $database;
    protected $reference;

    public function __construct(Database $database) {
        $this->middleware('auth');
        $this->database = $database;
        $this->reference = $this->database->getReference('scalers');
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
        $scalers = $this->reference->getValue();

        return view('scalers.index', compact('activities','scalers'));
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
            'nik' => ['required', 'string', 'max:16', 'min:16'],
            'nama' => ['required', 'string', 'max:30'],
            'jenis' => ['required', 'string']
        ]);

        $key = '';
        $scaler = '';

        try {
            $scaler = [
                'nik' => filter_input(INPUT_POST, $request->nik, FILTER_SANITIZE_STRING),
                'nama' => filter_input(INPUT_POST, $request->nama, FILTER_SANITIZE_STRING),
                'jenis' => filter_input(INPUT_POST, $request->jenis, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->reference->set($scaler);
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

        $message = "A scaler has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "nik : " . $scaler['nik'] . "<br>".
        "nama : " . $scaler['nama'] . "<br>".
        "jenis : " . $scaler['jenis'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('scaler')
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
            'nik' => ['required', 'string', 'max:16', 'min:16'],
            'nama' => ['required', 'string', 'max:30'],
            'jenis' => ['required', 'string']
        ]);
        
        $scaler = '';

        try {
            $scaler = $this->database->getReference('scalers/'.$request->key)->getValue();
            $scaler['nik'] = $request->nik;
            $scaler['nama'] = $request->nama;
            $scaler['jenis'] = $request->jenis;
            $scaler['updated_at'] = now();

            $this->database->getReference('scalers/'.$request->key)->update($scaler);
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

        $message = "A scaler has just updated with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "nik : " . $scaler['nik'] . "<br>".
        "nama : " . $scaler['nama'] . "<br>".
        "jenis : " . $scaler['jenis'] . "<br>";

        $this->storeActivity("Update", $message);

        return redirect('scaler')
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
            $scaler = $this->database->getReference('scalers/'.$request->key)->getValue();
            $this->database->getReference('scalers/'.$request->key)->remove();
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

        $message = "A scaler has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "nik : " . $scaler['nik'] . "<br>".
        "nama : " . $scaler['nama'] . "<br>".
        "jenis : " . $scaler['jenis'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('scaler')
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
            $this->database->getReference('scaler')->set(null);
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
            "You have just deleted scaler record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Scaler has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('scaler')
            ->with(["condition" => $condition, "notif" => $notif]);
    }
}
