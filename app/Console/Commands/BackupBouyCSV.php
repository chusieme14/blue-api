<?php

namespace App\Console\Commands;

use App\Models\BouyData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BackupBouyCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-bouy-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = now()->format('m');
        $buoyIds = ['2251', '2252', '2253', '2254'];
        
        foreach ($buoyIds as $key => $id) {
            $datas = BouyData::where('device_id', $id)->where('is_backup', 0)->get();
            if(sizeof($datas) < 1) continue;

            $filename = "buoy_{$id}_{$month}.csv";

            $csvPath = public_path('csv/' . $filename);

            $fileExists = file_exists($csvPath);
            $output = fopen($csvPath, 'a');

            if(!$fileExists){
                $headers = array_keys($datas[0]->toArray());
                fputcsv($output, $headers);
            }

            foreach ($datas as $row) {
                fputcsv($output, $row->toArray());
            }

            fclose($output);

            BouyData::where('device_id', $id)->where('is_backup', 0)->update(['is_backup' => 1]);

            // Upload to FTP
            // Storage::disk('ftp')->put("backup_csv/{$filename}", file_get_contents($csvPath));
            $remotePath = "backup_csv/{$filename}";

            $this->copyToFTP($csvPath, $remotePath);
        }
    }

    private function copyToFTP($localFilePath, $remoteFilePath){
        // $localFilePath = public_path('csv/buoy_2251_06.csv');
        // $remoteFilePath = 'buoy_2251_06.csv';

        $ftp_host = config('app.ftp_host');
        $ftp_user = config('app.ftp_username');
        $ftp_pass = config('app.ftp_password');

        $fp = fopen($localFilePath, 'r');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "ftp://$ftp_host/$remoteFilePath"); // use ftp://, not ftps://
        curl_setopt($ch, CURLOPT_USERPWD, "$ftp_user:$ftp_pass");
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFilePath));


        // Enable TLS (Explicit FTPS) over port 21
        curl_setopt($ch, CURLOPT_USE_SSL, CURLUSESSL_ALL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_PORT, 21); // use explicit port 21

        $result = curl_exec($ch);

        if (!$result) {
            echo "❌ Error: " . curl_error($ch) . PHP_EOL;
        } else {
            echo "✅ File uploaded successfully via FTPS." . PHP_EOL;
        }

        curl_close($ch);
        fclose($fp);

        // $ftp_host = env('FTP_HOST');
        // $ftp_user = env('FTP_USERNAME');
        // $ftp_pass = env('FTP_PASSWORD');
        // $ftp_port = 21;

        // // $local_file = public_path('csv/buoy_2251_06.csv'); // Your file
        // // $remote_file = 'buoy_2251_06.csv'; // Remote file path (can include folder)

        // // Connect to FTP
        // $conn = ftp_connect($ftp_host, $ftp_port);
        // dd(ftp_login($conn, $ftp_user, $ftp_pass));
        // if (!$conn) {
        //     die('❌ Could not connect to FTP server');
        // }

        // // Login
        // if (!ftp_login($conn, $ftp_user, $ftp_pass)) {
        //     ftp_close($conn);
        //     die('❌ Could not authenticate to FTP server');
        // }

        // // Set passive mode
        // ftp_pasv($conn, true);

        // // Upload file
        // if (ftp_put($conn, $remote_file, $local_file, FTP_BINARY)) {
        //     echo "✅ File uploaded successfully to FTP.\n";
        // } else {
        //     echo "❌ Failed to upload file to FTP.\n";
        // }

        // // Close connection
        // ftp_close($conn);
    }

    private function authenticate(){
        $response = Http::asJson()->post('https://blueapi.boggroup.net/api/v3/auth', [
            'type' => 'login',
            'username' => 'MyUser',
            'password' => 'MyPassword',
        ]);

        if ($response->successful()) {
            $data = $response->json(); // Get response as array
            $token = $data['token'] ?? null; // Adjust this based on the actual response structure
            echo "Logged in! Token: " . $token;
            return $token;
        } else {
            echo "Login failed. Status: " . $response->status();
            return null;
        }
    }

    private function generateCSV($datas){
        
    }
}
