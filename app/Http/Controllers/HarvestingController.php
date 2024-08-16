<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class HarvestingController extends Controller
{
    protected $database;
    protected $reference;

    public function __construct(Database $database) {
        $this->middleware('auth');
        $this->database = $database;
        $this->reference = $this->database->getReference('harvesting');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "TUK" || Auth::user()->role != "Harvesting") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }
        
        $activities = $this->activity();
        $harvesting = $this->reference->getValue();

        return view('harvesting.index', compact('activities','harvesting'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "TUK" || Auth::user()->role != "Harvesting") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $validator = $request->validateWithBag([
            'no_tebangan' => ['required', 'string', 'max:10'],
            'no_petak' => ['required', 'string', 'max:10'],
            'luasan' => ['required', 'string', 'max:10'],
            'jenis_tanaman' => ['required', 'string', 'max:10'],
            'tanggal_tanam' => ['required', 'date'],
            'kontraktor' => ['required', 'string', 'max:30'],
            'no_spk' => ['required', 'string', 'max:10'],
            'luas_tebangan' => ['required', 'double'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date']
        ]);

        $key = '';
        $harvesting = '';

        try {
            $harvesting = [
                'no_tebangan' => filter_input(INPUT_POST, $request->no_tebangan, FILTER_SANITIZE_STRING),
                'no_petak' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),
                'luasan' => filter_input(INPUT_POST, $request->luasan, FILTER_SANITIZE_STRING),
                'jenis_tanaman' => filter_input(INPUT_POST, $request->jenis_tanaman, FILTER_SANITIZE_STRING),
                'tanggal_tanam' => $request->tanggal_tanam,
                'kontraktor' => filter_input(INPUT_POST, $request->kontraktor, FILTER_SANITIZE_STRING),
                'no_spk' => filter_input(INPUT_POST, $request->no_spk, FILTER_SANITIZE_STRING),
                'luas_tebangan' => $request->luas_tebangan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_berakhir' => $request->tanggal_berakhir,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $this->reference->set($harvesting);
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

        $message = "A harvesting has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "no_tebangan : " . $harvesting['no_tebangan'] . "<br>".
        "no_petak : " . $harvesting['no_petak'] . "<br>".
        "kontraktor : " . $harvesting['kontraktor'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('harvesting')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "TUK" || Auth::user()->role != "Harvesting") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }

        $validator = $request->validateWithBag([
            'no_tebangan' => ['required', 'string', 'max:10'],
            'no_petak' => ['required', 'string', 'max:10'],
            'luasan' => ['required', 'string', 'max:10'],
            'jenis_tanaman' => ['required', 'string', 'max:10'],
            'tanggal_tanam' => ['required', 'date'],
            'kontraktor' => ['required', 'string', 'max:30'],
            'no_spk' => ['required', 'string', 'max:10'],
            'luas_tebangan' => ['required', 'double'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date']
        ]);

        $harvesting = '';

        try {
            $harvesting = $this->database->getReference('harvesting/'.$request->key)->getValue();
            $harvesting['no_tebangan'] = filter_input(INPUT_POST, $request->no_tebangan, FILTER_SANITIZE_STRING);
            $harvesting['no_petak'] = filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING);
            $harvesting['luasan'] = filter_input(INPUT_POST, $request->luasan, FILTER_SANITIZE_STRING);
            $harvesting['jenis_tanaman'] = filter_input(INPUT_POST, $request->jenis_tanaman, FILTER_SANITIZE_STRING);
            $harvesting['tanggal_tanam'] = $request->tanggal_tanam;
            $harvesting['kontraktor'] = filter_input(INPUT_POST, $request->kontraktor, FILTER_SANITIZE_STRING);
            $harvesting['no_spk'] = filter_input(INPUT_POST, $request->no_spk, FILTER_SANITIZE_STRING);
            $harvesting['luas_tebangan'] = $request->luas_tebangan;
            $harvesting['tanggal_mulai'] = $request->tanggal_mulai;
            $harvesting['tanggal_berakhir'] = $request->tanggal_berakhir;
            $harvesting['updated_at'] = now();

            $this->database->getReference('harvesting/'.$request->key)->set($harvesting);
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

        $message = "A harvesting has just updated with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_tebangan : " . $harvesting['no_tebangan'] . "<br>".
        "no_petak : " . $harvesting['no_petak'] . "<br>".
        "kontraktor : " . $harvesting['kontraktor'] . "<br>";

        $this->storeActivity("Update", $message);

        return redirect('harvesting')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "TUK" || Auth::user()->role != "Harvesting") {
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
            $harvesting = $this->database->getReference('harvesting/'.$request->key)->getValue();
            $this->database->getReference('harvesting/'.$request->key)->remove();
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

        $message = "A harvesting has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_tebangan : " . $harvesting['no_tebangan'] . "<br>".
        "no_petak : " . $harvesting['no_petak'] . "<br>".
        "kontraktor : " . $harvesting['kontraktor'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('harvesting')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function truncate()
    {
        if (Auth::user()->role != "Super Admin" || Auth::user()->role != "TUK" || Auth::user()->role != "Harvesting") {
            $this->toastNotification("Fails", "Failed, you're not allowed to enter this page");
            $condition = $this->getCondition();
            $notif = $this->getNotif();

            return redirect()
                ->back()
                ->with(["condition" => $condition, "notif" => $notif]);
        }
        
        try {
            $this->database->getReference('harvesting')->set(null);
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
            "You have just deleted harvesting record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Harvesting has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('harvesting')
            ->with(["condition" => $condition, "notif" => $notif]);
    }
}
