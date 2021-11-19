<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreTestController extends Controller
{
    public function index()
    {
        return view('pre_test.index'); 
    }

    public function check(Request $request)
    {
        $this->validate($request, [
            'nomor_kontainer'        => 'required|numeric|digits:7',
        ]);

        //cek posisi kontainer berdasarkan ketentuan
        $nomor_kontainer = $request->nomor_kontainer;
        $status = 'reject';
        $previous_number = 0;
        $angka0 = 0;
        $selain_bilangan_prima = 0;
        for ($i = 0; $i < 7; $i++) 
        {
            $number = mb_substr($nomor_kontainer, $i, 1);
            $var_arr[$i] =  $number;

            //posisi tengah
            if($this->cekBilanganPrima($number) == 1 && $number != 0){
                if($angka0 > 0 && $selain_bilangan_prima > 0 && $i < 4){
                    $status = 'DEAD';
                }elseif($i > 3 && $this->cekBilanganPrima($number) == 1 && $previous_number == $number){
                    $status = 'RIGHT';
                }elseif($i > 4 && $this->cekBilanganPrima($number) == 1 && $this->cekBilanganPrima($previous_number . $number) && $previous_number == $number - 1){
                    $status = 'LEFT';
                }elseif($i > 2 && $this->cekBilanganPrima($number) == 1){
                    $status = 'CENTRAL';
                }
            }else{
                $angka0 += 1;
                $selain_bilangan_prima += 1;
            }

            $previous_number = $number;
        }
        
        return redirect()->route('pre_test')->with(['nomor_kontainer' => $nomor_kontainer, 'status' => $status]);
        
    }

    private function cekBilanganPrima($number){
        if ($number == 1)
        return 0;
        for ($i = 2; $i <= $number/2; $i++){
            if ($number % $i == 0)
                return 0;
        }
        return 1;
    }
    
}
