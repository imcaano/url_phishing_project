<?php

require_once __DIR__ . '/../models/FileHandler.php';

class FileController {
    private $fileHandler;

    public function __construct() {
        $this->fileHandler = new FileHandler();
    }

    public function readFile($request) {
        try {
            $filePath = $request['filePath'] ?? null;
            
            if (!$filePath) {
                return [
                    'status' => 'error',
                    'message' => 'File path is required'
                ];
            }

            $content = $this->fileHandler->readFile($filePath);
            
            return [
                'status' => 'success',
                'data' => $content
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function writeFile($request) {
        try {
            $filePath = $request['filePath'] ?? null;
            $content = $request['content'] ?? null;

            if (!$filePath || $content === null) {
                return [
                    'status' => 'error',
                    'message' => 'File path and content are required'
                ];
            }

            $this->fileHandler->writeFile($filePath, $content);
            
            return [
                'status' => 'success',
                'message' => 'File written successfully'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
} 