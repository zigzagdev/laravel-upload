<?php

namespace App\Packages\Controller;

use App\Http\Controllers\Controller;
use App\Packages\Application\UseCase\UploadFileUseCase;
use App\Packages\Application\UseCommand\UploadFileUseCommand;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FileController extends Controller
{
    public function upload(
        Request $request,
        UploadFileUseCase $uploadFileUseCase
    ): JsonResponse {
        try {
            $useCommand = new UploadFileUseCommand($request);
            DB::beginTransaction();
            $uploadFileUseCase->handle($useCommand);

            DB::commit();
            return response()->json(
                data: null,
                status: Response::HTTP_OK,
                options: JSON_UNESCAPED_UNICODE
            );

        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return response()->json(
                ['message' => 'Internal Server Error'],
                500
            );
        }
    }
}
