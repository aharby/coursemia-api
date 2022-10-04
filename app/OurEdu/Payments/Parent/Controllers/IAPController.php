<?php

namespace App\OurEdu\Payments\Parent\Controllers;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Payments\Parent\Requests\AddMoneyToWalletRequest;
use App\OurEdu\Payments\Parent\Requests\SubscribeByIAPRequest;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Payments\UseCases\IAPUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class IAPController extends BaseApiController
{
    protected PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private ParserInterface $parserInterface;
    private IAPUseCase $iapUseCase;
    private $user;

    /**
     * @param ParserInterface $parserInterface
     * @param IAPUseCase $iapUseCase
     * @param PaymentTransactionRepositoryInterface $paymentTransactionRepository
     */
    public function __construct(
        ParserInterface $parserInterface,
        IAPUseCase $iapUseCase,
        PaymentTransactionRepositoryInterface $paymentTransactionRepository,
    ) {
        $this->parserInterface = $parserInterface;
        $this->iapUseCase = $iapUseCase;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->user = Auth::guard('api')->user();
        $this->middleware('type:student');
    }


    public function subscribe(SubscribeByIAPRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->parserInterface->deserialize($request->getContent())->getData();
            $data->customer_email = $this->user->email;
            $data->customer_ip = $request->ip();

            $iapProduct = $this->iapUseCase->create($this->user, $data);

            if (isset($iapProduct["status"])) {
                return formatErrorValidation($iapProduct);
            }
            DB::commit();
            return ["meta" => ["in_app_purchase_product" => $iapProduct]];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /**
     * @throws OurEduErrorException
     */
    public function verify(Request $request)
    {
        $receipt = $request->receipt_data;
        try {
            $error = $this->iapUseCase->verify($this->user, $receipt);
            if ($error) {
                return formatErrorValidation($error, $error['status']);
            }
        } catch (\Exception $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }

        return ["meta" => ["msg" => "paid_successfully"]];
    }

    /**
     * @throws OurEduErrorException
     */
    public function cancelSubscription()
    {
        try {
            $canceled = $this->iapUseCase->cancelSubscription($this->user);
            if (isset($canceled['status'])) {
                return formatErrorValidation($canceled);
            }
        } catch (\Exception $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }

        return ["meta" => ["msg" => "subscription cancelled"]];
    }
}
