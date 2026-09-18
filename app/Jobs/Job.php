namespace App\Jobs;

use App\Models\WhatsappMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $messageId;

    public function __construct($messageId)
    {
        $this->messageId = $messageId;
    }

    public function handle()
    {
        $record = WhatsappMessage::find($this->messageId);

        if (!$record || $record->status !== 'pending') {
            return;
        }

        $baseUrl = 'https://whatsapp.rusofterp.in/api/send';
        $params = [
            'username' => 'rusoft',
            'message'  => $record->message,
            'token'    => '1dc0f86d3dfb9c192037b3c1d82cdd99',
            'type'     => 'send',
            'number'   => '91' . $record->mobile,
        ];

        $url = $baseUrl . '?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $record->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } else {
            $record->update(['status' => 'failed']);
        }
    }
}
