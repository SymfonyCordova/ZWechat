<?php


namespace Zler\Wechat\Laravel\Controller;

use Illuminate\Http\Request;

class GzhController extends Controller
{
    public function token(Request $request){
        $requiredFields = array(
            'signature',
            'timestamp',
            'nonce',
            'echostr',
        );

        $fields = $this->checkRequiredFields($requiredFields, $request->query->all());

        if($this->getGzh()->checkSignature($fields)){
            return $fields['echostr'];
        }

        return $this->createSuccessResponse();
    }

    public function message(Request $request)
    {

    }

}