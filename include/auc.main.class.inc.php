<?php

##################################################################################
#
# File				: advancedUploadClass main class file containing auc class 
#					  and TrigerErrorException class.
# Class Title		: advancedUploadClass
# Class Description	: This class is used to handle the uploading of files.
#					  With advanced feature such as checking file size, checking
#					  file type, etc.
# Class Notes		: Please let me know if you have any questions / problems 
#					  / suggestions relating to this class.
# Copyright			: 2007
# Licence			: http://www.gnu.org/copyleft/gpl.html GNU var License
# Author 			: Mark Davidson <design@fluxnetworks.co.uk> 
#					  <http://design.fluxnetworks.co.uk>
# Created Date     	: 05/03/2007
# Last Modified    	: 10/03/2007
#
##################################################################################

class auc {

    public $disk_file_name = 'AAA';
    public $errors = array(); //array used to store any errors that occur.
    public $upload_dir = ''; //the upload_dir being used by the script
    public $make_safe = false; //default don't modify the file name to safe version
    public $max_file_size = 20971520; //Max File Size in Bytes, 20MB
    public $overwrite = false; //default don't overwrite files that already exsist
    public $check_file_type = false; //don't check for file type by default but can check for allowed and denied files.
    public $allowed_mime_types = array('image/jpeg', 'image/png', 'image/gif', 'image/tiff'); //array of allowed mime types used when check_file_type is set to allowed
    public $denied_mime_types = array('application/x-php', 'text/html'); //array of denied mime types used when check_file_type is set to denied

    /**
     * Check if the upload dir is valid, if it is not valid attempt to make the dir, if dir is succesfully created chmod it to 0777. 
     * If any elments fail return false else set upload_dir and return true.
     * @param string $dir
     * @param boolean $mkdir
     * @return true or false
     */

    public function upload_dir($dir, $mkdir = false) {
        $errors = & $this->errors;
        $status = true;

        if (!is_dir($dir)) {
            if ($mkdir) {
                if (!mkdir($dir)) {
                    $status = false;
                } else {
                    if (!chmod($dir, 0777)) {
                        $status = false;
                    }
                }
            } else {
                $status = false;
            }
        }

        if ($status) {
            $this->upload_dir = $dir;
            return true;
        } else {
            $errors['general'][] = 'Upload Dir is Not Valid and/or a dir could not be created/chmod.';
            return false;
        }
    }

    /**
     * check that the upload dir is valid and that it is writeable
     *
     * @param string $dir
     * @return true or false
     */
    public function check_dir($dir) {
        if (!is_dir($dir) || !is_writable($dir)) {
            return false;
        }
        return true;
    }

    /**
     * make the uploaded file name safe
     *
     * @param string $file_name
     * @return safe file name
     */
    public function make_safe($file_name) {
        return str_replace(' ', '_', $file_name);
    }

    /**
     * Check the Attemted Uploads for errors etc if everything goes good move the file, to the upload_dir.
     *
     * @param array $object
     * @return unknown
     */
    public function upload($object) {
        $errors = & $this->errors;

        if (empty($errors['general'])) {
            if (empty($this->upload_dir)) {
                $this->upload_dir = dirname(__FILE__) . '/'; //if no default upload_dir has been specified used the current dir.
            }
            
            if ($this->check_dir($this->upload_dir)) {
                $files = $_FILES[$object];
                $count = count($files['name']) - 1;

                /* echo '<pre>';
                  var_dump($files);
                  echo '</pre>'; */

                for ($current = 0; $current <= $count; $current++) {
                    $error = '';
                    try {
                        //check for $_FILES Errors
                        switch ($files['error'][$current]) {
                            case 0 : break;
                            case 1 : $error = $files['name'][$current] . ' exceeds the upload_max_filesize directive in php.ini';
                                break;
                            case 2 : $error = $files['name'][$current] . ' exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                                break;
                            case 3 : $error = $files['name'][$current] . ' was only partially uploaded';
                                break;
                            case 4 : $error = 'No file was uploaded';
                                break;
                            case 6 : $error = 'Missing a temporary folder';
                                break;
                            case 7 : $error = 'Failed to write ' . $files['name'][$current] . ' to disk';
                                break;
                            case 8 : $error = $files['name'][$current] . ' stopped by extension';
                                break;
                            default : $error = 'Unidentified Error, caused by ' . $files['name'][$current];
                                break;
                        }
                        if ($error) {
                            throw new TrigerErrorException($error, $files['name'][$current]);
                        }

                        //check that the file is not empty
                        if ($files['size'][$current] <= 0) {
                            throw new TrigerErrorException($files['name'][$current] . ' is empty', $files['name'][$current]);
                        }

                        //check that the file does not exceed the defined max_file_size
                        if ($this->max_file_size) {
                            if ($files['size'][$current] >= $this->max_file_size)
                                throw new TrigerErrorException($files['name'][$current] . ' exceeds defined max_file_size', $files['name'][$current]);
                        }

                        if ($this->check_file_type == 'allowed' && !in_array($files['type'][$current], $this->allowed_mime_types)) {
                            throw new TrigerErrorException($files['name'][$current] . ' is not an allowed type', $files['name'][$current]);
                        } elseif ($this->check_file_type == 'denied' && in_array($files['type'][$current], $this->denied_mime_types)) {
                            throw new TrigerErrorException($files['name'][$current] . ' is a denied type', $files['name'][$current]);
                        }

                        //if make_safe is true call make safe function		
                        if ($this->make_safe) {
                            $files['name'][$current] = $this->make_safe($files['name'][$current]);
                        }

                        //if overwrite is false and the file exists error
                        if (!$this->overwrite && file_exists($this->upload_dir . $files['name'][$current])) {
                            throw new TrigerErrorException($files['name'][$current] . ' already exsists', $files['name'][$current]);
                        }

                        //if rename is true, rename file name
                        if ($this->rename) {
                            $files['name'][$current] = $this->disk_file_name;
                            //$files['name'][$current] = disk_file_name;	
                        }
                        //move the uploaded file, error if anything goes wrong.
                        if (!move_uploaded_file($files['tmp_name'][$current], $this->upload_dir . $files['name'][$current])) {
                            throw new TrigerErrorException($files['name'][$current] . ' could not be moved', $files['name'][$current]);
                        }
                    } catch (TrigerErrorException $e) {
                        $errors[$files['name'][$current]][] = $e->Message();
                    }
                }

                if (empty($errors)) {
                    //return true if there where no errors
                    return true;
                } else {
                    //return the errors array if there where any errros
                    return $errors;
                }
            } else {
                //return false as dir is not valid
                $errors['general'][] = "The Specified Dir is Not Valid or is Not Writeable";
                return false;
            }
        }
    }

}

/**
 * Handle the Exceptions trigered by errors within upload code.
 *
 */
class TrigerErrorException extends Exception {

    protected $file = "";

    public function __construct($message, $file = "", $code = 0) {
        $this->file = $file;
        parent::__construct($message, $code);
    }

    public function Message() {
        return "{$this->message}";
    }

}

?>