<?php


namespace App\Constraints;

use Symfony\Component\HttpFoundation\Request;

class ValidatorUtils
{

    public function getParams( Request $req):array
    {
        $parameters = [];


        $params = json_decode($req->getContent()) ;

        if( $params) {
            foreach ($params as $param => $value) {
                $parameters[$param] = $value;
            }
        }
        return $parameters;

    }

    public function validateParams( $params, $constraints, $validator ) : array
    {

        $errors =  $validator->validate($params, $constraints );


        if( count($errors) >0 ) {
            $status = 400;
            $response = self::getErrorResponse($errors);
        } else
        {
            $status = 200;
            $response = self::getOkResponse( $params );

        }

        return  [
            'status' => $status,
            'response'=> $response
        ];
    }

    private function getErrorResponse ( $errors ): array
    {
        $response = [
            "ok"  => false,
            "msg" => []
        ];


        foreach ( $errors as $message)
        {
            $response['msg'][] = [
                'field' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage()
            ];

        }

        return $response;

    }

    private function getOkResponse ( $params ): array
    {

        $response = [
            "ok" => true
        ];

        foreach ($params as $param => $value )
        {
            $response[$param] =  $value ;
        }
        return $response;
    }
}