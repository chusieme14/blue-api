<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-file';

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
        $files = File::files(public_path('csv'));
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $csvPath = public_path('csv/' . $filename);
            $remotePath = "backup_csv/{$filename}";

            $this->copyToFTP($csvPath, $remotePath);
        }
    }

    private function copyToFTP($localFilePath, $remoteFilePath)
    {
        // $localFilePath = public_path('csv/buoy_2251_06.csv');
        // $remoteFilePath = 'buoy_2251_06.csv';

        $ftp_host = '51.44.37.11';
        $ftp_user = 'haliopftp';
        $ftp_pass = 'X7pL9qT2m';
        
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
}
