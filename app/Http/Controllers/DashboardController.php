<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Member;

class DashboardController extends Controller
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

            $total_members = count($this->getMembers());
            $total_disputes = count($this->getDisputes());

            $data = array(
                'page' => 'Dashboard',
                'total_members' => $total_members,
                'total_disputes' => $total_disputes
            );
            return view('admin.dashboard',compact('user','data'));
        }
    }

    public function getMembers($firstname = "", $middlename = "", $surname = ""){
        $client = new \GuzzleHttp\Client(['http_errors' => true]);

        $url = config('services.api.host') . "/restsql/res/Member?_output=application/json&_limit=50&_offset=0";

        if($firstname != "" && $middlename != "" && $surname != ""){
            $url .= "&FIRSTNAME=";
            $url .= $firstname;
            $url .= "&MIDDLENAME=";
            $url .= $middlename;
            $url .= "&SURNAME=";
            $url .= $surname;
        }

        try{
            $response = $client->request('GET', $url);
            $response_json = json_decode($response->getBody());

            if($response_json->members)
            {
                return $response_json->members;
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
