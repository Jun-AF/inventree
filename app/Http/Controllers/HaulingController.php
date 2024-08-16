<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class HaulingController extends Controller
{
    protected $database;
    protected $reference_28;
    protected $reference_42;

    public function __construct(Database $database) {
        $this->middleware('auth');
        $this->database = $database;
        $this->reference_28 = $this->database->getReference('hauling_24');
        $this->reference_42 = $this->database->getReference('hauling_42');
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
        $hauling = $this->reference_28->getValue();

        return view('tuk.hauling.hauling_28.index', compact('activities','hauling'));
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
            'no_petak' => ['required', 'string', 'max:10'],
            'kontraktor_harvesting' => ['required', 'string', 'max:30'],
            'no_spk_harvesting' => ['required', 'string', 'max:20'],
            'kontraktor_hauling' => ['required', 'string', 'max:30'],
            'no_spk_hauling' => ['required', 'string', 'max:20'],
            'tanggal_hauling' => ['required', 'date'],
            'no_batang' => ['required', 'interger'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'sortimen' => ['required', 'string', 'max:15'],
            'no_tumpukan' => ['required', 'integer'],
            'operator' => ['required', 'string', 'max:30'],
            'no_alat' => ['required', 'string', 'max:10'],
            'driver' => ['required', 'string', 'max:30'],
            'no_alat_angkut' => ['required', 'string', 'max:10'],
            'no_trip_angkutan' => ['required', 'string', 'max:10'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);

        $key = '';
        $hauling = '';

        try {
            $hauling = [
                'no_petak' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),
                'kontraktor_harvesting' => filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING),
                'no_spk_harvesting' => filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING),
                'kontraktor_hauling' => filter_input(INPUT_POST, $request->kontraktor_hauling, FILTER_SANITIZE_STRING),
                'no_spk_hauling' => filter_input(INPUT_POST, $request->no_spk_hauling, FILTER_SANITIZE_STRING),
                'tanggal_hauling' => $request->tanggal_hauling,
                'no_batang' => $request->no_batang,
                'jenis_kayu' => filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING),
                'sortimen' => filter_input(INPUT_POST, $request->sortimen, FILTER_SANITIZE_STRING),
                'no_tumpukan' => $request->no_tumpukan,
                'operator' => filter_input(INPUT_POST, $request->operator, FILTER_SANITIZE_STRING),
                'no_alat' => filter_input(INPUT_POST, $request->no_alat, FILTER_SANITIZE_STRING),
                'driver' => filter_input(INPUT_POST, $request->driver, FILTER_SANITIZE_STRING),
                'no_alat_angkut' => filter_input(INPUT_POST, $request->no_alat_angkut, FILTER_SANITIZE_STRING),
                'no_trip_angkutan' => filter_input(INPUT_POST, $request->no_trip_angkutan, FILTER_SANITIZE_STRING),
                'scaler' => filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING),
                'pengawas' => filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->reference_28->set($hauling);
            $key = $this->reference_28->limitToLast(1)->getSnapshot();
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
            "You have just created a hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A hauling 28 28 has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "no_petak : " . $hauling['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $hauling['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $hauling['jenis_kayu'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('hauling_28')
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
            'no_petak' => ['required', 'string', 'max:10'],
            'kontraktor_harvesting' => ['required', 'string', 'max:30'],
            'no_spk_harvesting' => ['required', 'string', 'max:20'],
            'kontraktor_hauling' => ['required', 'string', 'max:30'],
            'no_spk_hauling' => ['required', 'string', 'max:20'],
            'tanggal_hauling' => ['required', 'date'],
            'no_batang' => ['required', 'interger'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'sortimen' => ['required', 'string', 'max:15'],
            'no_tumpukan' => ['required', 'integer'],
            'operator' => ['required', 'string', 'max:30'],
            'no_alat' => ['required', 'string', 'max:10'],
            'driver' => ['required', 'string', 'max:30'],
            'no_alat_angkut' => ['required', 'string', 'max:10'],
            'no_trip_angkutan' => ['required', 'string', 'max:10'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);

        $hauling = '';

        try {
            $hauling = $this->database->getReference('hauling_28/'.$request->key)->getValue();
            $hauling['no_petak'] = filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING);
            $hauling['kontraktor_harvesting'] = filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING);
            $hauling['no_spk_harvesting'] = filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING);
            $hauling['kontraktor_hauling'] = filter_input(INPUT_POST, $request->kontraktor_hauling, FILTER_SANITIZE_STRING);
            $hauling['no_spk_hauling'] = filter_input(INPUT_POST, $request->no_spk_hauling, FILTER_SANITIZE_STRING);
            $hauling['tanggal_hauling'] = $request->tanggal_hauling;
            $hauling['no_batang'] = $request->no_batang;
            $hauling['jenis_kayu'] = filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING);
            $hauling['sortimen'] = filter_input(INPUT_POST, $request->sortimen, FILTER_SANITIZE_STRING);
            $hauling['no_tumpukan'] = $request->no_tumpukan;
            $hauling['operator'] = filter_input(INPUT_POST, $request->operator, FILTER_SANITIZE_STRING);
            $hauling['no_alat'] = filter_input(INPUT_POST, $request->no_alat, FILTER_SANITIZE_STRING);
            $hauling['driver'] = filter_input(INPUT_POST, $request->driver, FILTER_SANITIZE_STRING);
            $hauling['no_alat_angkut'] = filter_input(INPUT_POST, $request->no_alat_angkut, FILTER_SANITIZE_STRING);
            $hauling['no_trip_angkutan'] = filter_input(INPUT_POST, $request->no_trip_angkutan, FILTER_SANITIZE_STRING);
            $hauling['scaler'] = filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING);
            $hauling['pengawas'] = filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING);
            $hauling['updated_at'] = now();

            $this->database->getReference('hauling_28/'.$request->key)->update($hauling);
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
            "You have just created a hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A hauling_28 28 has just created with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $hauling['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $hauling['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $hauling['jenis_kayu'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('hauling_28_28')
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
            $measurement = $this->database->getReference('hauling_28/'.$request->key)->getValue();
            $this->database->getReference('hauling_28/'.$request->key)->remove();
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
            "You have just deleted a hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 28 has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('hauling_28')
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
            $this->database->getReference('hauling_28')->set(null);
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
            "You have just deleted hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Measurement 28 has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('hauling_28')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index_42()
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
        $hauling = $this->reference_42->getValue();

        return view('tuk.hauling.hauling_42.index', compact('activities','hauling'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_42(Request $request)
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
            'no_petak' => ['required', 'string', 'max:10'],
            'kontraktor_harvesting' => ['required', 'string', 'max:30'],
            'no_spk_harvesting' => ['required', 'string', 'max:20'],
            'kontraktor_hauling' => ['required', 'string', 'max:30'],
            'no_spk_hauling' => ['required', 'string', 'max:20'],
            'tanggal_hauling' => ['required', 'date'],
            'no_batang' => ['required', 'interger'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'operator' => ['required', 'string', 'max:30'],
            'no_alat' => ['required', 'string', 'max:10'],
            'driver' => ['required', 'string', 'max:30'],
            'no_alat_angkut' => ['required', 'string', 'max:10'],
            'no_trip_angkutan' => ['required', 'string', 'max:10'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);

        $key = '';
        $hauling = '';

        try {
            $hauling = [
                'no_petak' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),
                'kontraktor_harvesting' => filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING),
                'no_spk_harvesting' => filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING),
                'kontraktor_hauling' => filter_input(INPUT_POST, $request->kontraktor_hauling, FILTER_SANITIZE_STRING),
                'no_spk_hauling' => filter_input(INPUT_POST, $request->no_spk_hauling, FILTER_SANITIZE_STRING),
                'tanggal_hauling' => $request->tanggal_hauling,
                'no_batang' => $request->no_batang,
                'jenis_kayu' => filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING),
                'operator' => filter_input(INPUT_POST, $request->operator, FILTER_SANITIZE_STRING),
                'no_alat' => filter_input(INPUT_POST, $request->no_alat, FILTER_SANITIZE_STRING),
                'driver' => filter_input(INPUT_POST, $request->driver, FILTER_SANITIZE_STRING),
                'no_alat_angkut' => filter_input(INPUT_POST, $request->no_alat_angkut, FILTER_SANITIZE_STRING),
                'no_trip_angkutan' => filter_input(INPUT_POST, $request->no_trip_angkutan, FILTER_SANITIZE_STRING),
                'scaler' => filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING),
                'pengawas' => filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->database->getReference('hauling_42/'.$request->key)->set($hauling);
            $key = $this->reference_28->limitToLast(1)->getSnapshot();
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
            "You have just created a hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A hauling_42 28 has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "no_petak : " . $hauling['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $hauling['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $hauling['jenis_kayu'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('hauling_42_28')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_42(Request $request)
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
            'no_petak' => ['required', 'string', 'max:10'],
            'kontraktor_harvesting' => ['required', 'string', 'max:30'],
            'no_spk_harvesting' => ['required', 'string', 'max:20'],
            'kontraktor_hauling' => ['required', 'string', 'max:30'],
            'no_spk_hauling' => ['required', 'string', 'max:20'],
            'tanggal_hauling' => ['required', 'date'],
            'no_batang' => ['required', 'interger'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'operator' => ['required', 'string', 'max:30'],
            'no_alat' => ['required', 'string', 'max:10'],
            'driver' => ['required', 'string', 'max:30'],
            'no_alat_angkut' => ['required', 'string', 'max:10'],
            'no_trip_angkutan' => ['required', 'string', 'max:10'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);

        $hauling = '';

        try {
            $hauling = $this->database->getReference('hauling_42/'.$request->key)->getValue();
            $hauling['no_petak'] = filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING);
            $hauling['kontraktor_harvesting'] = filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING);
            $hauling['no_spk_harvesting'] = filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING);
            $hauling['kontraktor_hauling'] = filter_input(INPUT_POST, $request->kontraktor_hauling, FILTER_SANITIZE_STRING);
            $hauling['no_spk_hauling'] = filter_input(INPUT_POST, $request->no_spk_hauling, FILTER_SANITIZE_STRING);
            $hauling['tanggal_hauling'] = $request->tanggal_hauling;
            $hauling['no_batang'] = $request->no_batang;
            $hauling['jenis_kayu'] = filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING);
            $hauling['operator'] = filter_input(INPUT_POST, $request->operator, FILTER_SANITIZE_STRING);
            $hauling['no_alat'] = filter_input(INPUT_POST, $request->no_alat, FILTER_SANITIZE_STRING);
            $hauling['driver'] = filter_input(INPUT_POST, $request->driver, FILTER_SANITIZE_STRING);
            $hauling['no_alat_angkut'] = filter_input(INPUT_POST, $request->no_alat_angkut, FILTER_SANITIZE_STRING);
            $hauling['no_trip_angkutan'] = filter_input(INPUT_POST, $request->no_trip_angkutan, FILTER_SANITIZE_STRING);
            $hauling['scaler'] = filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING);
            $hauling['pengawas'] = filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING);
            $hauling['updated_at'] = now();

            $this->database->getReference('hauling_42/'.$request->key)->set($hauling);
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
            "You have just created a hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A hauling 28 has just created with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $hauling['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $hauling['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $hauling['jenis_kayu'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('hauling_28')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy_42(Request $request)
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
            $measurement = $this->database->getReference('hauling_42/'.$request->key)->getValue();
            $this->database->getReference('hauling_42/'.$request->key)->remove();
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
            "You have just deleted a hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 42 has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('hauling_42')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function truncate_42()
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
            $this->database->getReference('hauling_42')->set(null);
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
            "You have just deleted hauling record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Measurement 42 has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('hauling_42')
            ->with(["condition" => $condition, "notif" => $notif]);
    }
}
