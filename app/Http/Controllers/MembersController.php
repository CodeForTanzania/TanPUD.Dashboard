<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Member;

class MembersController extends Controller
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
            $members = $this->getMembers();

            $data = array(
                'page' => 'Members',
                'members' => $members
            );
            return view('admin.members.main',compact('user','data'));
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

            if(isset($request->firstname) && isset($request->middlename) && isset($request->surname)){
                $member = $this->getMembers($request->firstname, $request->middlename, $request->surname);
                
                if($member){
                    $data = array(
                        'page' => 'Members',
                        'member' => $member[0]
                    );
                    return view('admin.members.view',compact('user','data'));
                }
                else return redirect('members');
            }
            else return redirect('members');
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
}