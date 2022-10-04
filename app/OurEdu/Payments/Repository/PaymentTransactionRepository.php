<?php

namespace App\OurEdu\Payments\Repository;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\PaymentMethodsEnum;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Models\AppleIAPProduct;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Payment transaction Repository
 */
class PaymentTransactionRepository implements PaymentTransactionRepositoryInterface
{

    public function create($data, $method = null)
    {
        return PaymentTransaction::create($data);
    }

    public function findBy($key, $value)
    {
        return PaymentTransaction::where($key, $value)->first();
    }

    public function findByWith($key, $value, $with)
    {
        return PaymentTransaction::where($key, $value)->with($with)->first();
    }

    public function updateByOrderNumber($reference, $data)
    {
        $transaction = tap(PaymentTransaction::where('merchant_reference', $reference)->first())->update($data);
        return $transaction->refresh();
    }

    public function sentPaymentTransactions($user, $isPaginate = true)
    {
        $payments = $user->sentPaymentTransactions()->completed()
            ->when(request('user_id'), function ($q) {
                $q->where('receiver_id', request()->get('user_id'));
            })
            ->when(request('date_from'), function ($q) {
                $q->whereDate('created_at', '>=', startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q) {
                $q->whereDate('created_at', '<=', endOfDay(request('date_to')));
            })
            ->with('receiver.student.educationalSystem.country', 'sender');
        if ($isPaginate) {
            return $payments->latest()->jsonPaginate();
        }

        return $payments->get();
    }

    public function paymentTransactionsCountByPeriod($user, string $period): array
    {
        $lang = config('app.locale') == 'ar' ? "ar_EG" : "en_US";
        DB::statement("SET lc_time_names = " . $lang);

        Carbon::setlocale(config('app.locale'));


        return match ($period) {
            "week" => $this->paymentTransactionsCountByWeek($user),
            "month" => $this->paymentTransactionsCountByMonth($user),
            "year" => $this->paymentTransactionsCountByYear($user),
            default => $this->paymentTransactionsCountByWeek($user)
        };
    }

    private function paymentTransactionsCountByWeek($user): array
    {
        $date_from = Carbon::now()->startOfweek(Carbon::FRIDAY)->format('Y-m-d');
        $date_to = Carbon::now()->endOfweek(Carbon::THURSDAY)->format('Y-m-d');

        $data = $user->sentPaymentTransactions()
            ->whereDate('created_at', '>=', $date_from)
            ->where('status', 'Completed')
            ->selectRaw('DAYNAME (created_at) day,DAY (created_at) order_column,SUM(amount) as total')
            ->orderBy('order_column', 'asc')
            ->groupBy(['order_column', 'day'])
            ->pluck(
                'total',
                'day',
            )->toArray();

        $currency = $user->country->currency_code;

        $array = [];
        $range = CarbonPeriod::create($date_from, $date_to);

        foreach ($range as $dt) {
            $dates = new \stdClass();
            $dates->key = $dt->translatedFormat('l');
            $dates->value = $data[$dt->translatedFormat('l')] ?? 0;
            $dates->currency = $currency;
            $array[] = $dates;
        }

        return $array;
    }

    private function paymentTransactionsCountByMonth($user)
    {
        $date_from = Carbon::now()->startOfMonth()->toDateString();
        $date_to = Carbon::now()->endOfMonth()->toDateString();

        $data = $user->sentPaymentTransactions()
            ->whereDate('created_at', '>=', $date_from)
            ->where('status', 'Completed')
            ->selectRaw('DATE (created_at) date,SUM(amount) as total')
            ->orderBy('date')
            ->groupBy(['date'])
            ->pluck(
                'total',
                'date',
            )
            ->toArray();

        $currency = $user->country->currency_code;

        $range = CarbonPeriod::create($date_from, $date_to);
        $array = [];

        foreach ($range as $dt) {
            $dates = new \stdClass();
            $dates->key = $dt->format('j');
            $dates->value = $data[$dt->toDateString()] ?? 0;
            $dates->currency = $currency;
            $array[] = $dates;
        }

        return $array;
    }

    private function paymentTransactionsCountByYear($user)
    {
        $today = Carbon::now();

        $date = $today->startOfYear();

        $data = $user->sentPaymentTransactions()
            ->whereDate('created_at', '>=', $date->format('Y-m-d'))
            ->where('status', 'Completed')
            ->selectRaw('MONTHNAME (created_at) month,MONTH (created_at) order_column,SUM(amount) as total')
            ->orderBy('order_column', 'asc')
            ->groupBy(['order_column', 'month'])
            ->pluck(
                'total',
                'month',
            )
            ->toArray();

        $currency = $user->country->currency_code;

        $range = $this->getLastTwelveMonthNames($date);
        $array = [];

        foreach ($range as $dt) {
            $dates = new \stdClass();
            $dates->key = $dt;
            $dates->value = $data[$dt] ?? 0;
            $dates->currency = $currency;
            $array[] = $dates;
        }

        return $array;
    }

    private function getLastTwelveMonthNames($date)
    {
        $month_names[] = $date->translatedFormat('F');

        for ($i = 1; $i <= 11; $i++) {
            $month_names[$i] = ucfirst($date->addMonth()->translatedFormat('F'));
        }
        return $month_names;
    }

    public function childrenOwnTransactions(array $usersIds)
    {
        $query = PaymentTransaction::query()
            ->where('payment_method', PaymentEnums::VISA)
            ->completed()
            ->orderBy('created_at', 'desc')
            ->when(
                $childId = request()->input('user_id'),
                fn($q) => $q->where('sender_id', $childId),
                fn($q) => $q->whereIn('sender_id', $usersIds)
            )
            ->when(request()->has('from_date'), function ($q) {
                $q->whereDate(
                    'created_at',
                    '>=',
                    Carbon::parse(request()->input('from_date'))
                        ->format('Y-m-d')
                );
            })
            ->when(request()->has('to_date'), function ($q) {
                $q->whereDate(
                    'created_at',
                    '<=',
                    Carbon::parse(request()->input('to_date'))
                        ->format('Y-m-d')
                );
            });

        return $query->jsonPaginate();
    }

    public function all($filters = [], $paginated = false)
    {
        $query = PaymentTransaction::query()
            ->orderBy('created_at', 'desc')
            ->has('methodable')
            ->with(['methodable','sender','receiver'])
            ->when(isset($filters['from_date']), function ($q) use ($filters) {
                $q->whereDate(
                    'created_at',
                    '>=',
                    Carbon::parse($filters['from_date'])
                        ->format('Y-m-d')
                );
            })
            ->when(isset($filters['to_date']), function ($q) use ($filters) {
                $q->whereDate(
                    'created_at',
                    '<=',
                    Carbon::parse($filters['to_date'])
                        ->format('Y-m-d')
                );
            })->when(isset($filters['status']), function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            });

        return $paginated ? $query->paginate() : $query->get();
    }
    public function studentReceivedPaymentTransactions($children, $isPaginate = true)
    {
        $payments = PaymentTransaction::query()
            ->whereIn('receiver_id', $children)
            ->where(function ($query) {
                $query->where('payment_transaction_type', TransactionTypesEnums::REFUND);
                $query->orWhere(function ($qu) {
                    $qu->where('payment_transaction_type', TransactionTypesEnums::DEPOSIT);
                    $qu->where('payment_transaction_for', PaymentEnums::ADD_MONEY_WALLET);
                });
            })
            ->when(request('user_id'), function ($q) {
                $q->where('receiver_id', request()->get('user_id'));
            })
            ->when(request('date_from'), function ($q) {
                $q->whereDate('created_at', '>=', startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q) {
                $q->whereDate('created_at', '<=', endOfDay(request('date_to')));
            })
            ->completed()
            ->with('receiver.student.educationalSystem.country', 'sender');
        if ($isPaginate) {
            return $payments->latest()->jsonPaginate();
        }

        return $payments->get();
    }

    public function getTotalUsersTransactionsSpending(array $usersIds)
    {
        $totalSpending = 0;
        $walletAmount = 0;
        $users = User::query()
            ->whereIn('id', $usersIds)
            ->with('student')
            ->with([
                'childrenSpentPaymentTransactions' => function ($query) {
                    $query->where('payment_method', PaymentEnums::WALLET)
                        ->where('payment_transaction_type', TransactionTypesEnums::WITHDRAWAL)
                        ->where('status', PaymentEnums::COMPLETED);
                }
            ])
            ->withSum(
                [
                    'childrenSpentPaymentTransactions' => function ($query) {
                        $query->where('payment_method', PaymentEnums::WALLET)
                            ->where('payment_transaction_type', TransactionTypesEnums::WITHDRAWAL)
                            ->where('status', PaymentEnums::COMPLETED);
                    }
                ],
                'amount'
            )
            ->orderBy('id', 'desc')
            ->get();

        foreach ($users as $user) {
            $totalSpending += $user->children_spent_payment_transactions_sum_amount;
            $walletAmount += $user->student?->wallet_amount;
        }

        $userTransactionsObject = new stdClass();
        $userTransactionsObject->total_spending = $totalSpending;
        $userTransactionsObject->wallet_amount = $walletAmount;
        return $userTransactionsObject;
    }

    public function getChildrenExpensesTransactions(array $usersIds, bool $isPaginate = true)
    {
        $query = PaymentTransaction::query()
            ->where('payment_method', PaymentEnums::WALLET)
            ->where('payment_transaction_type', TransactionTypesEnums::WITHDRAWAL)
            ->orderBy('id', 'desc')
            ->with('detail.subscribable');

        if (!request()->has('user_id')) {
            $query = $query->whereIn('receiver_id', $usersIds);
        }
        if (request()->has('user_id')) {
            $query = $query->where('receiver_id', request()->get('user_id'));
        }
        if (request()->has('from_date')) {
            $query = $query->whereDate(
                'created_at',
                '>=',
                Carbon::parse(request()->get('from_date'))
                    ->format('Y-m-d')
            );
        }
        if (request()->has('to_date')) {
            $query = $query->whereDate(
                'created_at',
                '<=',
                Carbon::parse(request()->get('to_date'))
                    ->format('Y-m-d')
            );
        }

        return $isPaginate ? $query->jsonPaginate() : $query->get();
    }

    public function getUsersTransactionsSpending(array $usersIds)
    {
        return User::query()->with(['student.educationalSystem.country'])
            ->whereIn('id',$usersIds)
            ->with([
                'childrenSpentPaymentTransactions' => function ($query) {
                    $query->where('payment_method', PaymentEnums::WALLET)
                        ->where('payment_transaction_type', TransactionTypesEnums::WITHDRAWAL)
                        ->where('status', PaymentEnums::COMPLETED);
                }
            ])
            ->withSum(
                [
                    'childrenSpentPaymentTransactions' => function ($query) {
                        $query->where('payment_method', PaymentEnums::WALLET)
                            ->where('payment_transaction_type', TransactionTypesEnums::WITHDRAWAL)
                            ->where('status', PaymentEnums::COMPLETED);
                    }
                ],
                'amount'
            )
            ->orderBy('id', 'desc')
            ->jsonPaginate();
    }

    public function getTotalDeposit()
    {
        return PaymentTransaction::query()
            ->completed()
            ->deposit()
            ->sum('amount');
    }

    public function getTotalWithdraw($query = null)
    {
        if (!$query) {
            $query = PaymentTransaction::query()
                ->completed();
        }
        return $this->paymentTransactionForFilterQuery($query)
            ->sum('amount');
    }


    // filtering payment transactions according to payment transaction for value
    private function paymentTransactionForFilterQuery($query)
    {
        return $query->where(function ($quer) {
            $quer->when(request('product_type'), function ($q) {
                $q = $this->paymentTransactionsFilters($q);
                $q->where('payment_transaction_for', request('product_type'))
                    ->when(request('product_type') == PaymentEnums::ADD_MONEY_WALLET, function ($query) {
                        $query->where(function ($qu) {
                            $qu->deposit();
                        })->orWhere(function ($qu) {
                            $qu->where('payment_transaction_type', TransactionTypesEnums::REFUND)
                                ->whereHas('parentTransaction', function ($q) {
                                    $q->where('payment_method', PaymentMethodsEnum::VISA);
                                });
                        });
                    })
                    ->when(request('product_type') != PaymentEnums::ADD_MONEY_WALLET, function ($query) {
                        $query->whereHas('detail', function ($query) {
                            //course type and course id filter
                            $query->when(
                                request('product_type') == PaymentEnums::COURSE && request('course_id'),
                                function ($query) {
                                    $query->where('subscribable_id', request('course_id'));
                                }
                            )
                                // course type with no course id filter and product id
                                ->when(
                                    request('product_type') == PaymentEnums::COURSE && !request('course_id') && request(
                                        'product_id'
                                    ),
                                    function ($query) {
                                        $courses = Course::where('is_top_qudrat', 1)
                                            ->withTrashed()
                                            ->where('instructor_id', request('product_id'))
                                            ->pluck('id')->toArray();
                                        $query->whereIn('subscribable_id', $courses);
                                    }
                                )
                                // filter by course type only
                                ->when(
                                    request('product_type') == PaymentEnums::COURSE && !request(
                                        'course_id'
                                    ) && !request('product_id'),
                                    function ($query) {
                                        $courses = Course::where('is_top_qudrat', 1)
                                            ->withTrashed()
                                            ->pluck('id')->toArray();
                                        $query->whereIn('subscribable_id', $courses);
                                    }
                                )
                                //vcr spot with product id
                                ->when(
                                    request('product_type') == PaymentEnums::VCR_SPOT && request('product_id'),
                                    function ($query) {
                                        $instructorSessions = VCRRequest::where('instructor_id', request('product_id'))
                                            ->where('status', '!=', VCRRequestStatusEnum::REJECTED)
                                            ->pluck('id')->toArray();
                                        $query->whereIn('subscribable_id', $instructorSessions);
                                    }
                                )
                                //vcr spot without product id
                                ->when(
                                    request('product_type') == PaymentEnums::VCR_SPOT && !request('product_id'),
                                    function ($query) {
                                        $instructorSessions = VCRRequest::where(
                                            'status',
                                            '!=',
                                            VCRRequestStatusEnum::REJECTED
                                        )
                                            ->pluck('id')->toArray();
                                        $query->whereIn('subscribable_id', $instructorSessions);
                                    }
                                )
                                // subject and product id
                                ->when(
                                    request('product_type') == PaymentEnums::SUBJECT && request('product_id'),
                                    function ($query) {
                                        $query->where('subscribable_id', request('product_id'));
                                    }
                                );
                        });
                    });
            })
                ->when(!request('product_type'), function ($query) {
                    $query->where(function ($qu) {
                        $qu = $this->paymentTransactionsFilters($qu);
                        $qu->where('payment_transaction_for', PaymentEnums::ADD_MONEY_WALLET)
                            ->where(function ($qu) {
                                $qu->deposit();
                            })
                            ->orWhere(function ($qu) {
                                $qu->where('payment_transaction_type', TransactionTypesEnums::REFUND)
                                    ->whereHas('parentTransaction', function ($q) {
                                        $q->where('payment_method', PaymentMethodsEnum::VISA);
                                    });
                            });
                    })
                        ->orWhere(function ($qu) {
                            $qu = $this->paymentTransactionsFilters($qu);
                            $qu->where('payment_transaction_for', PaymentEnums::COURSE)
                                ->whereHas('detail', function ($query) {
                                    $courses = Course::where('is_top_qudrat', 1)
                                        ->withTrashed()
                                        ->pluck('id')->toArray();
                                    $query->whereIn('subscribable_id', $courses);
                                });
                        })
                        ->orWhere(function ($qu) {
                            $qu = $this->paymentTransactionsFilters($qu);
                            $qu->where('payment_transaction_for', PaymentEnums::VCR_SPOT)
                                ->whereHas('detail', function ($query) {
                                    $instructorSessions = VCRRequest::where(
                                        'status',
                                        '!=',
                                        VCRRequestStatusEnum::REJECTED
                                    )
                                        ->pluck('id')->toArray();
                                    $query->whereIn('subscribable_id', $instructorSessions);
                                });
                        })->orWhere(function ($qu) {
                            $qu = $this->paymentTransactionsFilters($qu);
                            $qu->where('payment_transaction_for', PaymentEnums::SUBJECT);
                        });
                });
        });
    }

    private function paymentTransactionsFilters($query)
    {
        return $query->when(request('from_date'), function ($q) {
            $q->whereDate('created_at', '>=', startOfDay(request('from_date')));
        })
            ->when(request('to_date'), function ($q) {
                $q->whereDate('created_at', '<=', endOfDay(request('to_date')));
            })
            ->when(request('payment_method'), function ($q) {
                $q->where('payment_method', request('payment_method'));
            });
    }

    public function paymentTransactionsReport()
    {
        $query = PaymentTransaction::query()
            ->completed()
            ->with(['receiver', 'detail.subscribable']);

        return $this->paymentTransactionForFilterQuery($query)->orderBy('id','desc');
    }

    public function getIAPProduct($transaction)
    {
        $offset = $transaction->payment_transaction_for == PaymentEnums::COURSE ?
            PaymentEnums::COURSE_OFFSET : PaymentEnums::SUBJECT_OFFSET;
        $subscribable = $transaction->detail->subscribable;
        return AppleIAPProduct::query()
            ->where('product_id', '>', $offset)
            ->where('price', '>', $subscribable->subscription_cost)
            ->first();
    }

    public function hasPendingIAPTransactions($user): bool
    {
        return PaymentTransaction::query()->where("sender_id", $user->id)
            ->where('payment_method', PaymentEnums::IAP)
            ->where("status", PaymentEnums::PENDING)->exists();
    }
}
