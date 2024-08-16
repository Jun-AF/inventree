<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class PartnerController extends Controller
{
    protected $database;
    protected $reference;

    public function __construct(Database $database) {
        $this->middleware('auth');
        $this->database = $database;
        $this->reference = $this->database->getReference('partners');
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
        $jenis_bu = ['BUMN','BUMD','Swasta'];
        $partners = $this->reference->getValue();

        return view('partners.index', compact('activities','partners'));
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
            'no_rekanan' => ['required', 'string', 'max:10'],
            'kontraktor' => ['required', 'string', 'max:30'],
            'jenis_bu' => ['required', 'string', 'max:7'],
            'nama_direktur' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'npwp' => ['required', 'string', 'max:16'],
            'no_rekening' => ['required', 'string', '15'],
            'jenis_pekerjaan' => ['required', 'string', '20'],
            'no_petak' => ['required', 'string', 'max:10'],
            'jenis_tanaman' => ['required', 'string'],
            'driver' => ['required', 'string', 'max:30'],
            'no_alat_angkut' => ['required', 'string', 'max:10'],
            'sortimen_kayu' => ['required', 'string', 'max:10'],
            'no_pbph' => ['required', 'string', 'max:10'],
            'no_spk' => ['required', 'string', 'max:10'],
            'tanggal_berlaku' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date']
        ]);

        $key = '';
        $partner = '';

        try {
            $partner = [
                'no_rekanan' => filter_input(INPUT_POST, $request->no_rekanan, FILTER_SANITIZE_STRING),
                'kontraktor' => filter_input(INPUT_POST, $request->kontraktor, FILTER_SANITIZE_STRING),
                'jenis_bu' => filter_input(INPUT_POST, $request->jenis_bu, FILTER_SANITIZE_STRING),
                'nama_direktur' => filter_input(INPUT_POST, $request->nama_direktur, FILTER_SANITIZE_STRING),
                'alamat' => filter_input(INPUT_POST, $request->alamat, FILTER_SANITIZE_STRING),
                'npwp' => filter_input(INPUT_POST, $request->npwp, FILTER_SANITIZE_STRING),
                'no_rekening' => filter_input(INPUT_POST, $request->no_rekening, FILTER_SANITIZE_STRING),
                'jenis_pekerjaan' => filter_input(INPUT_POST, $request->jenis_pekerjaan, FILTER_SANITIZE_STRING),
                'no_petak' => filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING),
                'jenis_tanaman' => filter_input(INPUT_POST, $request->jenis_tanaman, FILTER_SANITIZE_STRING),
                'driver' => filter_input(INPUT_POST, $request->driver, FILTER_SANITIZE_STRING),
                'no_alat_angkut' => filter_input(INPUT_POST, $request->no_alat_angkut, FILTER_SANITIZE_STRING),
                'sortimen_kayu' => filter_input(INPUT_POST, $request->sortimen_kayu, FILTER_SANITIZE_STRING),
                'no_pbph' => filter_input(INPUT_POST, $request->no_pbph, FILTER_SANITIZE_STRING),
                'no_spk' => filter_input(INPUT_POST, $request->no_spk, FILTER_SANITIZE_STRING),
                'tanggal_berlaku' => filter_input(INPUT_POST, $request->tanggal_berlaku, FILTER_SANITIZE_STRING),
                'tanggal_berakhir' => filter_input(INPUT_POST, $request->tanggal_berakhir, FILTER_SANITIZE_STRING),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->reference->set($partner);
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

        $message = "A partner has just created with <br>" . 
        "uniqueid : " . $key->getKey() . "<br>".
        "no_rekanan : " . $partner['no_rekanan'] . "<br>".
        "kontraktor : " . $partner['kontraktor'] . "<br>";

        $this->storeActivity("Store", $message);

        return redirect('partner')
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
            'no_rekanan' => ['required', 'string', 'max:10'],
            'kontraktor' => ['required', 'string', 'max:30'],
            'jenis_bu' => ['required', 'string', 'max:7'],
            'nama_direktur' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'npwp' => ['required', 'string', 'max:16'],
            'no_rekening' => ['required', 'string', '15'],
            'jenis_pekerjaan' => ['required', 'string', '20'],
            'no_petak' => ['required', 'string', 'max:10'],
            'jenis_tanaman' => ['required', 'string'],
            'driver' => ['required', 'string', 'max:30'],
            'no_alat_angkut' => ['required', 'string', 'max:10'],
            'sortimen_kayu' => ['required', 'string', 'max:10'],
            'no_pbph' => ['required', 'string', 'max:10'],
            'no_spk' => ['required', 'string', 'max:10'],
            'tanggal_berlaku' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date']
        ]);

        $partner = '';

        try {
            $partner = $this->database->getReference('partners/'.$request->key)->getValue();
            $partner['no_rekanan'] = filter_input(INPUT_POST, $request->no_rekanan, FILTER_SANITIZE_STRING);
            $partner['kontraktor'] = filter_input(INPUT_POST, $request->kontraktor, FILTER_SANITIZE_STRING);
            $partner['jenis_bu'] = filter_input(INPUT_POST, $request->jenis_bu, FILTER_SANITIZE_STRING);
            $partner['nama_direktur'] = filter_input(INPUT_POST, $request->nama_direktur, FILTER_SANITIZE_STRING);
            $partner['alamat'] = filter_input(INPUT_POST, $request->alamat, FILTER_SANITIZE_STRING);
            $partner['npwp'] = filter_input(INPUT_POST, $request->npwp, FILTER_SANITIZE_STRING);
            $partner['no_rekening'] = filter_input(INPUT_POST, $request->no_rekening, FILTER_SANITIZE_STRING);
            $partner['jenis_pekerjaan'] = filter_input(INPUT_POST, $request->jenis_pekerjaan, FILTER_SANITIZE_STRING);
            $partner['no_petak'] = filter_input(INPUT_POST, $request->no_petak, FILTER_SANITIZE_STRING);
            $partner['jenis_tanaman'] = filter_input(INPUT_POST, $request->jenis_tanaman, FILTER_SANITIZE_STRING);
            $partner['driver'] = filter_input(INPUT_POST, $request->driver, FILTER_SANITIZE_STRING);
            $partner['no_alat_angkut'] = filter_input(INPUT_POST, $request->no_alat_angkut, FILTER_SANITIZE_STRING);
            $partner['sortimen_kayu'] = filter_input(INPUT_POST, $request->sortimen_kayu, FILTER_SANITIZE_STRING);
            $partner['no_pbph'] = filter_input(INPUT_POST, $request->no_pbph, FILTER_SANITIZE_STRING);
            $partner['no_spk'] = filter_input(INPUT_POST, $request->no_spk, FILTER_SANITIZE_STRING);
            $partner['tanggal_berlaku'] = filter_input(INPUT_POST, $request->tanggal_berlaku, FILTER_SANITIZE_STRING);
            $partner['tanggal_berakhir'] = filter_input(INPUT_POST, $request->tanggal_berakhir, FILTER_SANITIZE_STRING);
            $partner['updated_at'] = now();

            $this->database->getReference('partners/'.$request->key)->update($partner);
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

        $message = "A partner has just updated with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_rekanan : " . $partner['no_rekanan'] . "<br>".
        "kontraktor : " . $partner['kontraktor'] . "<br>";

        $this->storeActivity("Update", $message);

        return redirect('partner')
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
            $partner = $this->database->getReference('partners/'.$request->key)->getValue();
            $this->database->getReference('partners/'.$request->key)->remove();
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

        $message = "A partner has just deleted with <br>" . 
        "uniqueid : " . $request->key . "<br>".
        "no_rekanan : " . $partner['no_rekanan'] . "<br>".
        "kontraktor : " . $partner['kontraktor'] . "<br>";

        $this->storeActivity("Delete", $message);

        return redirect('partner')
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
            $this->database->getReference('partner')->set(null);
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
            "You have just deleted partner record"
        );

        $condition = $this->getCondition();
        $notif = $this->getNotif();

        $message = "Partner has just deleted";

        $this->storeActivity("Delete", $message);

        return redirect('partner')
            ->with(["condition" => $condition, "notif" => $notif]);
    }
}
