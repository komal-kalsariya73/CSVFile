<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class CsvController extends Controller
{
    public function index()
    {
        return view('import_csv');
    }

    public function upload()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'csv_file' => 'uploaded[csv_file]|ext_in[csv_file,csv]|max_size[csv_file,2048]',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['status' => 'error', 'validation_errors' => $validation->getErrors()]);
        }

        $file = $this->request->getFile('csv_file');
        if (!$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'validation_errors' => ['The file upload failed.']]);
        }

        $filePath = WRITEPATH . 'uploads/' . $file->getName();
        $file->move(WRITEPATH . 'uploads', $file->getName());

        $userModel = new UserModel();
        $importSuccess = true;
        $header = true;
        $dataInserted = 0;

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($header) {
                    $header = false;
                    continue;
                }

                if (count($data) < 3 || empty($data[0]) || empty($data[1]) || empty($data[2])) {
                    $importSuccess = false;
                    break;
                }

                if (!filter_var($data[1], FILTER_VALIDATE_EMAIL)) {
                    $importSuccess = false;
                    return $this->response->setJSON(['status' => 'error', 'validation_errors' => ['Invalid email format in row: ' . implode(', ', $data)]]);
                }

                if ($userModel->where('email', $data[1])->first()) {
                    $importSuccess = false;
                    return $this->response->setJSON(['status' => 'error', 'validation_errors' => ['Email already exists: ' . $data[1]]]);
                }

                if (!is_numeric($data[2])) {
                    $importSuccess = false;
                    return $this->response->setJSON(['status' => 'error', 'validation_errors' => ['Age must be numeric in row: ' . implode(', ', $data)]]);
                }

                $userData = [
                    'name' => $data[0],
                    'email' => $data[1],
                    'age' => $data[2],
                ];

                if (!$userModel->insert($userData)) {
                    $importSuccess = false;
                    break;
                }

                $dataInserted++;
            }

            fclose($handle);

            if ($dataInserted === 0) {
                return $this->response->setJSON(['status' => 'error', 'validation_errors' => ['No  data to  tabel import.']]);
            }

            if ($importSuccess && $dataInserted > 0) {
                return $this->response->setJSON(['status' => 'success', 'message' => "$dataInserted row(s) imported successfully."]);
            } else {
                return $this->response->setJSON(['status' => 'error', 'validation_errors' => ['Invalid data in CSV file']]);
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'validation_errors' => ['Failed to open the CSV file']]);
        }
    }
}
