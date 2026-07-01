<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InvoiceAndUserSeeder extends Seeder
{
    /**
     * أنواع الأقمشة المستخدمة في النظام
     */
    private array $fabricTypes = [
        'قطن بلدي',
        'قطن مستورد',
        'بوليستر',
        'لينن',
        'حرير',
        'صوف',
        'اكريلك',
        'نايلون',
        'فيسكوز',
        'جوت',
        'كتان',
        'قطن كمبد',
        'تريكو قطن',
        'قطيفة',
        'شيفون',
        'ساتان',
        'تول',
        'بوبلين',
        'بمبو',
        'سباندكس',
    ];

    /**
     * ألوان الأقمشة
     */
    private array $fabricColors = [
        'أبيض', 'أسود', 'أزرق', 'أحمر', 'أخضر',
        'أصفر', 'بيج', 'بني', 'رمادي', 'وردي',
        'برتقالي', 'بنفسجي', 'كريمي', 'نيلي', 'تيركواز',
        'خوخي', 'خردلي', 'زيتي', 'بورجندي', 'لبني',
    ];

    /**
     * أسماء المرسلين (موردين)
     */
    private array $senders = [
        'شركة النيل للأقمشة',
        'مصنع المصري للغزل',
        'شركة القاهرة التجارية',
        'مؤسسة الدلتا للنسيج',
        'شركة الشرق الأوسط',
        'مصنع إسكندرية للأقمشة',
        'الشركة العالمية للاستيراد',
        'مؤسسة الخليج للأقمشة',
        'مصنع الوادي',
        'شركة سينا للتجارة',
        'مؤسسة رشيد للنسيج',
        'شركة المنصورة للأقمشة',
        'مصنع الإسماعيلية',
        'شركة بورسعيد التجارية',
        'مؤسسة طنطا للنسيج',
    ];

    /**
     * أسماء المستقبلين
     */
    private array $receivers = [
        'أحمد محمد',
        'محمود علي',
        'عمر إبراهيم',
        'خالد السيد',
        'يوسف أحمد',
        'طارق عبدالله',
        'إبراهيم حسن',
        'محمد سالم',
        'سامي نور',
        'هاني عبدالعزيز',
        'رامي خالد',
        'وليد مصطفى',
        'كريم شريف',
        'علاء الدين',
        'مدحت رضا',
    ];

    /**
     * أسماء للمستخدمين الجدد
     */
    private array $userNames = [
        'أحمد', 'محمود', 'عمر', 'خالد', 'يوسف',
        'طارق', 'إبراهيم', 'سامي', 'هاني', 'رامي',
        'وليد', 'كريم', 'علاء', 'مدحت', 'سعيد',
        'نادر', 'فادي', 'شريف', 'بسام', 'عادل',
        'ماجد', 'تامر', 'ناصر', 'عمار', 'زياد',
    ];

    public function run(): void
    {
        // ===== إنشاء المستخدمين =====
        $this->command->info('جاري إنشاء المستخدمين...');
        
        $officeRole  = Role::findByName('office', 'web');
        $storeRole   = Role::findByName('store', 'web');
        $managerRole = Role::findByName('manager', 'web');

        $officeUsers  = [];
        $storeUsers   = [];

        // 10 مستخدمي مكتب
        for ($i = 1; $i <= 10; $i++) {
            $name     = $this->userNames[array_rand($this->userNames)] . '_office_' . $i;
            $username = 'office' . str_pad($i, 2, '0', STR_PAD_LEFT);

            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'name'     => 'موظف مكتب ' . $i,
                    'email'    => null,
                    'password' => Hash::make('password'),
                ]
            );
            $user->syncRoles([$officeRole]);
            $officeUsers[] = $user;
        }

        // 10 مستخدمي مخزن
        for ($i = 1; $i <= 10; $i++) {
            $username = 'store' . str_pad($i, 2, '0', STR_PAD_LEFT);

            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'name'     => 'أمين مخزن ' . $i,
                    'email'    => null,
                    'password' => Hash::make('password'),
                ]
            );
            $user->syncRoles([$storeRole]);
            $storeUsers[] = $user;
        }

        // 3 مديرين
        for ($i = 1; $i <= 3; $i++) {
            $username = 'manager' . str_pad($i, 2, '0', STR_PAD_LEFT);

            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'name'     => 'مدير ' . $i,
                    'email'    => null,
                    'password' => Hash::make('password'),
                ]
            );
            $user->syncRoles([$managerRole]);
        }

        $this->command->info('✓ تم إنشاء 23 مستخدم جديد');

        // ===== إنشاء الفواتير =====
        $this->command->info('جاري إنشاء 1500 فاتورة...');

        $total    = 1500;
        $bar      = $this->command->getOutput()->createProgressBar($total);
        $bar->start();

        $counter  = Invoice::max('invoice_number') ? 
                    (int) ltrim(Invoice::max('invoice_number'), 'INV-') + 1 : 1;

        $allReceivers = $this->receivers;
        $allSenders   = $this->senders;

        for ($i = 0; $i < $total; $i++) {
            $receivedOffset = rand(0, 730); // تاريخ عشوائي في آخر سنتين
            $date           = now()->subDays(rand(0, 730))->format('Y-m-d');
            $status         = rand(0, 100) < 65 ? 'received' : 'pending'; // 65% مستلمة
            $invoiceNumber  = 'INV-' . str_pad($counter++, 5, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'sender'         => $allSenders[array_rand($allSenders)],
                'receiver'       => $allReceivers[array_rand($allReceivers)],
                'date'           => $date,
                'status'         => $status,
                'img'            => null,
            ]);

            // ما بين 1 و 8 أصناف في كل فاتورة
            $itemCount = rand(1, 8);
            for ($j = 0; $j < $itemCount; $j++) {
                $fabricType  = $this->fabricTypes[array_rand($this->fabricTypes)];
                $fabricColor = $this->fabricColors[array_rand($this->fabricColors)];
                $qty         = round(rand(5, 200) + (rand(0, 99) / 100), 3);
                $totalKg     = round($qty * rand(2, 8) + (rand(0, 99) / 100), 3);

                $deliveredQty   = $status === 'received' ? round($qty * (rand(85, 100) / 100), 3) : 0;
                $deliveredTotalKg = $status === 'received' ? round($deliveredQty * ($totalKg / $qty), 3) : 0;

                $codeNum  = rand(10000, 99999);

                InvoiceItem::create([
                    'invoice_id'          => $invoice->id,
                    'code'                => 'F' . $codeNum,
                    'type'                => $fabricType,
                    'fabric_color'        => $fabricColor,
                    'qty'                 => $qty,
                    'unit'                => 'رول',
                    'total_kg'            => $totalKg,
                    'delivered_qty'       => $deliveredQty,
                    'delivered_total_kg'  => $deliveredTotalKg,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info('✓ تم إنشاء 1500 فاتورة بنجاح!');
        $this->command->info('📊 ملخص:');
        $this->command->table(
            ['البيان', 'القيمة'],
            [
                ['إجمالي الفواتير', Invoice::count()],
                ['الفواتير المستلمة', Invoice::where('status', 'received')->count()],
                ['الفواتير المعلقة',  Invoice::where('status', 'pending')->count()],
                ['إجمالي الأصناف',   InvoiceItem::count()],
                ['إجمالي المستخدمين', User::count()],
            ]
        );
    }
}
