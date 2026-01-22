namespace Database\Seeders; // Correct Namespace

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@scms.out.co.tz'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123456789@'),
                'role' => 'super_admin' // Ensure you have a "role" column
            ]
        );
    }
}
