<?php
class Upload {
	function __construct() {
		$this->directory = time();
		$this->uploads_dir = STATICS_DIR .'uploads/';
		$this->allowedFileFormats = [
    	'jpg' => 'image/jpeg',
    	'png' => 'image/png',
			'pdf' => 'application/pdf',
			'doc' => 'application/msword',
			'odt' => 'application/vnd.oasis.opendocument.text',
			'zip' => 'application/zip',
			'jpeg' => 'image/jpeg'
		];
	}

	public function post() {
		if(count($_FILES) > 0) {
			$this->createDir();
			return $this->uploadFiles();
		} else {
			http_response_code(404);
			error_logs(['No files received', 007]);
			die(json_encode([
				'message' => 'Please send me some files',
				'code' => 007
			]));
		}
	}

	private function uploadFiles() {
		$files = [];
		foreach($_FILES as $index => $file) {
			try {
				$files[] = $this->validateFile($file);
			} catch(RuntimeException $e) {
				http_response_code(500);
				error_logs([$e->getMessage(), $e->getCode()]);
				die(json_encode([
					'message' => $e->getMessage(),
					'code' => $e->getCode()
				]));
			}
		}
		return [
			'message' => 'File(s) uploaded correctly',
			'url' => $GLOBALS['config']['statics_url'],
			'files' => $files
		];
	}

	private function validateFile($file) {
		if(!isset($file['error']) || is_array($file['error'])) {
			throw new RuntimeException('Invalid parameters', 006);
		}
		switch ($_FILES['upfile']['error']) {
    	case UPLOAD_ERR_OK:
      	break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException('No file received', 005);
      	break;
      case UPLOAD_ERR_INI_SIZE:
				throw new RuntimeException('Exceeded filesize limit', 003);
      	break;
      case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException('Exceeded filesize limit', 003);
				break;
			default:
				throw new RuntimeException('Unknown error', 004);
      	break;
		}
		if ($file['size'] > 1000000) {
			throw new RuntimeException('Exceeded filesize limit', 003);
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$ext = array_search($finfo->file($file['tmp_name']), $this->allowedFileFormats, true);
    if (false === $ext) {
			throw new RuntimeException('Invalid file format', 001);
		}
		$f = $this->directory .'/' .sha1_file($file['tmp_name']) .'.' .$ext;
		if (!move_uploaded_file($file['tmp_name'], $this->uploads_dir .$f)) {
        throw new RuntimeException('Failed to move uploaded file.', 002);
		}
		return [
			'file' => 'uploads/' .$f,
			'name' => $file['name'],
			'url' => $GLOBALS['config']['statics_url'] .'uploads/' .$f
		];
	}

	private function createDir() {
		if(!is_dir($this->uploads_dir .$this->directory)) {
			mkdir($this->uploads_dir .$this->directory, 0777, true);
		}
	}
}
?>
