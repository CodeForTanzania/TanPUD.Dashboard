<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class DisputesController extends Controller
{
    public function index()
    {
        // Checking for session.
        if(!Auth::check())
        {
            return redirect('login');
        }
        else{
            $user = Auth::user();
            $disputes = $this->getDisputes();

            $data = array(
                'page' => 'Disputes',
                'disputes' => $disputes
            );
            return view('admin.disputes.main',compact('user','data'));
        }
    }

    public function view(Request $request)
    {
        // Checking for session.
        if(!Auth::check())
        {
            return redirect('login');
        }
        else{
            $user = Auth::user();

            if(isset($request->uri)){
                $dispute = $this->getDisputes($request->uri);
                
                if($dispute){
                    $data = array(
                        'page' => 'Disputes',
                        'dispute' => $dispute[0]
                    );
                    return view('admin.disputes.view',compact('user','data'));
                }
                else return redirect('disputes');
            }
            else return redirect('disputes');
        }
    }

    public function getDisputes($uri = ""){
        $client = new \GuzzleHttp\Client(['http_errors' => true]);

        $url = config('services.api.host') . "/restsql/res/Dispute?_output=application/json&_limit=50&_offset=0";

        if($uri != ""){
            $url .= "&_URI=";
            $url .= $uri;
        }

        try{
            $response = $client->request('GET', $url);
            $response_json = json_decode($response->getBody());

            if($response_json->disputes)
            {
                return $response_json->disputes;
            }
            else{
                // No Users.
                return null;
            }
        }
        catch (ClientErrorResponseException $e) {
            \Log::info("Client error :" . $e->getResponse()->getBody(true));
            return null;
        }
        catch (ServerErrorResponseException $e) {
            \Log::info("Server error" . $e->getResponse()->getBody(true));
            return null;
        }
        catch (BadResponseException $e) {
            \Log::info("BadResponse error" . $e->getResponse()->getBody(true));
            return null;
        }
        catch (\Exception $e) {
            \Log::info("Err" . $e->getMessage());
            return null;
        }
    }
}