<?php

namespace App\Packages\Controller;

use App\Http\Controllers\Controller;
use App\Packages\Application\UseCase\UploadFileUseCase;
use App\Packages\Application\UseCommand\UploadFileUseCommand;
use App\Packages\Domain\Entity\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class FileController extends Controller
{
    public function upload(
        Request $request,
        UploadFileUseCase $uploadFileUseCase
    ): JsonResponse {
        try {
            $useCommand = new UploadFileUseCommand($request);

            $uploadFileUseCase->handle($useCommand);

            return response()->json(null, 201);

        } catch (Throwable $exception) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
