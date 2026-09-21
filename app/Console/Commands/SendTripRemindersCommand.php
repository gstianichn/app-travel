<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\TripReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTripRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travel:send-reminders {--booking= : ID booking tertentu untuk testing pengingat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat perjalanan H-2 Jam kepada penumpang dengan jadwal keberangkatan hari ini.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $specificBookingId = $this->option('booking');

        if ($specificBookingId) {
            $booking = Booking::with(['user', 'schedule.route.origin', 'schedule.route.destination', 'passengers.seat'])->find($specificBookingId);
            if (! $booking || ! $booking->user) {
                $this->error("Booking ID {$specificBookingId} tidak ditemukan atau tidak memiliki user terdaftar.");

                return Command::FAILURE;
            }

            $booking->user->notify(new TripReminderNotification($booking));
            $this->info("Notifikasi pengingat H-2 jam berhasil dikirim untuk Booking {$booking->booking_code} ke user {$booking->user->email}.");

            return Command::SUCCESS;
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $targetMinTime = $now->copy()->addMinutes(90)->format('H:i:s');
        $targetMaxTime = $now->copy()->addMinutes(150)->format('H:i:s');

        $eligibleBookings = Booking::with(['user', 'schedule.route.origin', 'schedule.route.destination', 'passengers.seat'])
            ->whereIn('status', ['confirmed', 'paid', 'success', 'lunas'])
            ->whereHas('schedule', function ($q) use ($today, $targetMinTime, $targetMaxTime) {
                $q->whereDate('departure_date', $today)
                    ->whereBetween('departure_time', [$targetMinTime, $targetMaxTime]);
            })
            ->get();

        $count = 0;
        foreach ($eligibleBookings as $booking) {
            if (! $booking->user) {
                continue;
            }

            // Hindari duplikasi pengingat dalam 6 jam terakhir
            $alreadyReminded = $booking->user->notifications()
                ->where('data->meta->booking_id', $booking->id)
                ->where('data->status_type', 'reminder_2h')
                ->where('created_at', '>=', Carbon::now()->subHours(6))
                ->exists();

            if (! $alreadyReminded) {
                $booking->user->notify(new TripReminderNotification($booking));
                $count++;
            }
        }

        $this->info("Berhasil memproses pengingat perjalanan H-2 Jam: {$count} notifikasi dikirim.");

        return Command::SUCCESS;
    }
}
