<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature-256');
        
        $localToken = config('app.deploy_secret', env('DEPLOY_SECRET', 'secret_password'));
        
        // Use either URL token (?token=SECRET) or GitHub Webhook Signature
        $isValid = false;
        
        if ($request->query('token') && $request->query('token') === $localToken) {
            $isValid = true;
        } elseif ($githubHash) {
            $localHash = 'sha256=' . hash_hmac('sha256', $githubPayload, $localToken, false);
            if (hash_equals($localHash, $githubHash)) {
                $isValid = true;
            }
        }
        
        if (!$isValid) {
            Log::warning('Deployment failed: Invalid secret or signature.');
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Log::info('Deployment triggered from Webhook.');

        // Execute the deployment script
        $result = Process::path(base_path())->run('sh deploy.sh');
        
        $output = $result->output();
        $errorOutput = $result->errorOutput();

        Log::info("Deployment Output:\n" . $output . "\n" . $errorOutput);

        return response()->json([
            'status' => 'success',
            'message' => 'Deployment executed.',
            'output' => $output,
            'error' => $errorOutput,
        ]);
    }
}
