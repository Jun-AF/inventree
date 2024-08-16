<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class MeasurementController extends Controller
{
    protected $database;
    protected $reference_28;
    protected $reference_42;

    public function __construct(Database $database) {
        $this->middleware('auth');
        $this->database = $database;
        $this->reference_28 = $this->database->getReference('measurement_24');
        $this->reference_42 = $this->database->getReference('measurement_42');
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
        $measurements = $this->reference_28->getValue();

        return view('tuk.measurement.measurement_28.index', compact('activities','measurements'));
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
            'no_spk_harvesting' => ['required', 'string', 'max:10'],
            'tanggal_ukur' => ['required', 'date'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'no_batang' => ['required', 'integer'],
            'sortimen' => ['required', 'string', 'max:10'],
            'no_tumpukan' => ['required', 'integer'],
            'panjang' => ['required', 'double'],
            'lebar' => ['required', 'double'],
            'tinggi' => ['required', 'double'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);

        $key = '';
        $measurement = '';

        $faktorPengali = 1.0; // Nilai default jika tidak ada faktor khusus
        
        if ($request->jenis_kayu === 'Eucalyptus') {
            $faktorPengali = 0.67;
        } elseif ($request->jenis_kayu === 'Accacia') {
            $faktorPengali = 0.59;
        } elseif ($request->jenis_kayu === 'Rimba Campuran') {
            $faktorPengali = 0.63;
        }

        $volume = $request->panjang * $request->lebar * $request->tinggi * $faktorPengali;

        try {
            $measurement = [
                'no_petak' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),
                'kontraktor_harvesting' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING),
                'no_spk_harvesting' => filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING),
                'tanggal_ukur' => $request->tanggal_ukur,
                'jenis_kayu' => filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING),
                'no_batang' => $request->no_batang,
                'sortimen' => filter_input(INPUT_POST, $request->sortimen, FILTER_SANITIZE_STRING),
                'no_tumpukan' => $request->no_tumpukan,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'tinggi' => $request->tinggi,
                'volume' => $volume,
                'scaler' => filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING),
                'pengawas' => filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->reference_28->set($measurement);
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
            "You have just created a measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 28 has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('measurement_28')
            ->with(["condition" => $condition, "notif" => $notif]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $key)
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
            'no_spk_harvesting' => ['required', 'string', 'max:10'],
            'tanggal_ukur' => ['required', 'date'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'no_batang' => ['required', 'integer'],
            'sortimen' => ['required', 'string', 'max:10'],
            'no_tumpukan' => ['required', 'integer'],
            'panjang' => ['required', 'double'],
            'lebar' => ['required', 'double'],
            'tinggi' => ['required', 'double'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);
        
        $measurement = '';

        $faktorPengali = 1.0; // Nilai default jika tidak ada faktor khusus
        
        if ($request->jenis_kayu === 'Eucalyptus') {
            $faktorPengali = 0.67;
        } elseif ($request->jenis_kayu === 'Accacia') {
            $faktorPengali = 0.59;
        } elseif ($request->jenis_kayu === 'Rimba Campuran') {
            $faktorPengali = 0.63;
        }

        $volume = $request->panjang * $request->lebar * $request->tinggi * $faktorPengali;

        try {
            $measurement = $this->database->getReference('measurement_28/'.$request->key)->getValue();
            $measurement['no_petak'] = filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING);
            $measurement['kontraktor_harvesting'] = filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING);
            $measurement['no_spk_harvesting'] = filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING);
            $measurement['tanggal_ukur'] = $request->tanggal_ukur;
            $measurement['jenis_kayu'] = filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING);
            $measurement['no_batang'] = $request->no_batang;
            $measurement['sortimen'] = filter_input(INPUT_POST, $request->sortimen, FILTER_SANITIZE_STRING);
            $measurement['no_tumpukan'] = $request->no_tumpukan;
            $measurement['panjang'] = $request->panjang;
            $measurement['lebar'] = $request->lebar;
            $measurement['tinggi'] = $request->tinggi;
            $measurement['volume'] = $volume;
            $measurement['scaler'] = filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING);
            $measurement['pengawas'] = filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING);
            $measurement['updated_at'] = now();

            $this->database->getReference('measurement_42/'.$request->key)->update($measurement);
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
            "You have just updated a measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 28 has just updated with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Update", $message);

        return redirect('measurement_28')
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
            $measurement = $this->database->getReference('measurement_28/'.$request->key)->getValue();
            $this->database->getReference('measurement_28/'.$request->key)->remove();
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
            "You have just deleted a measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 28 has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('measurement_28')
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
            $this->database->getReference('measurement_28')->set(null);
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
            "You have just deleted measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Measurement 28 has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('measurement_28')
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
        $measurements = $this->reference_28->getValue();

        return view('tuk.measurement.measurement_42.index', compact('activities','measurements'));
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
            'no_spk_harvesting' => ['required', 'string', 'max:10'],
            'tanggal_ukur' => ['required', 'date'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'no_batang' => ['required', 'integer'],
            'diameter' => ['required', 'double'],
            'panjang' => ['required', 'double'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);

        $key = '';
        $measurement = '';

        $faktorPengali = 1.0; // Nilai default jika tidak ada faktor khusus
        
        if ($request->jenis_kayu === 'Eucalyptus') {
            $faktorPengali = 0.67;
        } elseif ($request->jenis_kayu === 'Accacia') {
            $faktorPengali = 0.59;
        } elseif ($request->jenis_kayu === 'Rimba Campuran') {
            $faktorPengali = 0.63;
        }

        $volume = $request->diameter * $request->panjang * $faktorPengali;

        try {
            $measurement = [
                'no_petak' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),
                'kontraktor_harvesting' => filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING),
                'no_spk_harvesting' => filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING),
                'tanggal_ukur' => $request->tanggal_ukur,
                'jenis_kayu' => filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING),
                'no_batang' => $request->no_batang,
                'diameter' => $request->no_tumpukan,
                'panjang' => $request->panjang,
                'volume' => $volume,
                'scaler' => filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING),
                'pengawas' => filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->reference_42->set($measurement);
            $key = $this->reference_42->limitToLast(1)->getSnapshot();
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
            "You have just created a measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 42 has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('measurement_42')
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
            'no_spk_harvesting' => ['required', 'string', 'max:10'],
            'tanggal_ukur' => ['required', 'date'],
            'jenis_kayu' => ['required', 'string', 'max:15'],
            'no_batang' => ['required', 'integer'],
            'diameter' => ['required', 'double'],
            'panjang' => ['required', 'double'],
            'scaler' => ['required', 'string', 'max:30'],
            'pengawas' => ['required', 'string', 'max:30']
        ]);
        
        $measurement = '';

        $faktorPengali = 1.0; // Nilai default jika tidak ada faktor khusus
        
        if ($request->jenis_kayu === 'Eucalyptus') {
            $faktorPengali = 0.67;
        } elseif ($request->jenis_kayu === 'Accacia') {
            $faktorPengali = 0.59;
        } elseif ($request->jenis_kayu === 'Rimba Campuran') {
            $faktorPengali = 0.63;
        }

        $volume = $request->diameter * $request->panjang * $faktorPengali;

        try {
            $measurement = $this->database->getReference('measurement_42/'.$request->key)->getValue();
            $measurement['no_petak'] = filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING);
            $measurement['kontraktor_harvesting'] = filter_input(INPUT_POST, $request->kontraktor_harvesting, FILTER_SANITIZE_STRING);
            $measurement['no_spk_harvesting'] = filter_input(INPUT_POST, $request->no_spk_harvesting, FILTER_SANITIZE_STRING);
            $measurement['tanggal_ukur'] = $request->tanggal_ukur;
            $measurement['jenis_kayu'] = filter_input(INPUT_POST, $request->jenis_kayu, FILTER_SANITIZE_STRING);
            $measurement['no_batang'] = $request->no_batang;
            $measurement['diameter'] = $request->diameter;
            $measurement['panjang'] = $request->panjang;
            $measurement['volume'] = $volume;
            $measurement['scaler'] = filter_input(INPUT_POST, $request->scaler, FILTER_SANITIZE_STRING);
            $measurement['pengawas'] = filter_input(INPUT_POST, $request->pengawas, FILTER_SANITIZE_STRING);
            $measurement['updated_at'] = now();

            $this->database->getReference('measurement_42/'.$request->key)->update($measurement);
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
            "You have just updated a measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 42 has just created with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Update", $message);

        return redirect('measurement_42')
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
            $measurement = $this->database->getReference('measurement_42/'.$request->key)->getValue();
            $this->database->getReference('measurement_42/'.$request->key)->remove();
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
            "You have just deleted a measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "A measurement 42 has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_petak : " . $measurement['no_petak'] . "<br>".
        "kontraktor_harvesting : " . $measurement['kontraktor_harvesting'] . "<br>".
        "jenis_kayu : " . $measurement['jenis_kayu'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('measurement_42')
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
            $this->database->getReference('measurement_42')->set(null);
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
            "You have just deleted measurement record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Measurement 42 has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('measurement_42')
            ->with(["condition" => $condition, "notif" => $notif]);
    }
}
