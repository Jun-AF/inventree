<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class OperatorController extends Controller
{
    protected $database;
    protected $reference;

    public function __construct(Database $database) {
        $this->middleware('auth');
        $this->database = $database;
        $this->reference = $this->database->getReference('operators');
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
        $operators = $this->reference->getValue();

        return view('operators.index', compact('activities','operators'));
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
        $operator = '';

        try {
            $operator = [
                'nik' => filter_input(INPUT_POST, $request->nik, FILTER_SANITIZE_STRING),
                'nama' => filter_input(INPUT_POST, $request->nama, FILTER_SANITIZE_STRING),
                'jenis' => filter_input(INPUT_POST, $request->jenis, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->reference->set($operator);
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

        $message = "A operator has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "nik : " . $operator['nik'] . "<br>".
        "nama : " . $operator['nama'] . "<br>".
        "jenis : " . $operator['jenis'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('operator')
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
        
        $operator = '';

        try {
            $operator = $this->database->getReference('operators/'.$request->key)->getValue();
            $operator['nik'] = $request->nik;
            $operator['nama'] = $request->nama;
            $operator['jenis'] = $request->jenis;
            $operator['updated_at'] = now();

            $this->database->getReference('operators/'.$request->key)->update($operator);
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

        $message = "A operator has just updated with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "nik : " . $operator['nik'] . "<br>".
        "nama : " . $operator['nama'] . "<br>".
        "jenis : " . $operator['jenis'] . "<br>";

        $this->storeActivity("Update", $message);

        return redirect('operator')
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
            $operator = $this->database->getReference('operators/'.$request->key)->getValue();
            $this->database->getReference('operators/'.$request->key)->remove();
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

        $message = "A operator has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "nik : " . $operator['nik'] . "<br>".
        "nama : " . $operator['nama'] . "<br>".
        "jenis : " . $operator['jenis'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('operator')
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
            $this->database->getReference('operator')->set(null);
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
            "You have just deleted operator record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Operator has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('operator')
            ->with(["condition" => $condition, "notif" => $notif]);
    }
}
