<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorPayoutDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransferAccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $detail = $user->payoutDetail;

        if (! $detail) {
            $detail = new InstructorPayoutDetail(['user_id' => $user->id]);
        }

        $methods = InstructorPayoutDetail::methodLabels();

        return view('instructor.transfer-account.index', compact('detail', 'methods'));
    }

    public function store(Request $request)
    {
        $methods = array_keys(InstructorPayoutDetail::methodLabels());

        $validated = $request->validate([
            'payout_method' => ['required', Rule::in($methods)],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'account_number' => [
                Rule::requiredIf(in_array($request->input('payout_method'), [
                    InstructorPayoutDetail::METHOD_INSTAPAY,
                    InstructorPayoutDetail::METHOD_STC_PAY,
                ], true)),
                'nullable',
                'string',
                'max:255',
            ],
            'iban' => [
                Rule::requiredIf($request->input('payout_method') === InstructorPayoutDetail::METHOD_IBAN),
                'nullable',
                'string',
                'max:64',
            ],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payout_method.required' => 'اختر طريقة استلام المستحقات',
            'payout_method.in' => 'طريقة التحويل غير صحيحة',
            'account_holder_name.required' => 'اسم صاحب الحساب مطلوب',
            'account_number.required' => 'رقم الحساب / المعرّف مطلوب لهذه الطريقة',
            'iban.required' => 'رقم الآيبان (IBAN) مطلوب',
        ]);

        $method = $validated['payout_method'];
        $iban = null;
        $accountNumber = null;
        $bankName = null;

        if ($method === InstructorPayoutDetail::METHOD_IBAN) {
            $iban = strtoupper(preg_replace('/\s+/', '', (string) ($validated['iban'] ?? '')));
            $bankName = $validated['bank_name'] ?? null;
        } elseif ($method === InstructorPayoutDetail::METHOD_INSTAPAY) {
            $accountNumber = trim((string) ($validated['account_number'] ?? ''));
        } elseif ($method === InstructorPayoutDetail::METHOD_STC_PAY) {
            $accountNumber = trim((string) ($validated['account_number'] ?? ''));
        }

        $user = auth()->user();
        $user->payoutDetail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'payout_method' => $method,
                'account_holder_name' => $validated['account_holder_name'],
                'account_number' => $accountNumber,
                'iban' => $iban,
                'bank_name' => $bankName,
                'branch_name' => null,
                'swift_code' => null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('instructor.transfer-account.index')
            ->with('success', 'تم حفظ بيانات التحويل بنجاح. ستظهر للإدارة عند تحويل مستحقاتك.');
    }
}
