<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Order;

class OrderController extends Controller
{

    public function appoint()
    {
        // Получаем все заказы со статусом "поступил"
        $orders = Order::where('status', 'поступил')->get();

        foreach ($orders as $order) {
            // Находим всех активных водителей с балансом, не меньшим чем комиссия заказа и с рейтингом выше выше 0.5
            $eligibleDrivers = Driver::where('status', 'active')
                ->where('balance', '>=', $order->amount * 0.1) // 10% от суммы заказа
                ->where('rating', '>', 0.5) // Рейтинг водителя выше 0.5
                ->get();

            // Если нет подходящих водителей, пропускаем этот заказ
            if ($eligibleDrivers->isEmpty()) {
                continue;
            }

            // Инициализируем переменные для хранения ближайшего водителя и минимального расстояния
            $closestDriver = null;
            $minDistance = PHP_INT_MAX;
            // Используем максимальное значение для начального минимального расстояния

            // Рассчитываем расстояние между каждым водителем и точкой заказа
            foreach ($eligibleDrivers as $driver) {

                $distance = $this->calculateDistance(
                    $driver->latitude, $driver->longitude,
                    $order->startLatitude, $order->startLongitude
                );

                // Если текущее расстояние меньше минимального, обновляем ближайшего водителя и минимальное расстояние
                if ($distance < $minDistance) {
                    $closestDriver = $driver;
                    $minDistance = $distance;
                }
            }

            // Если найден ближайший водитель, связываем его с заказом и обновляем статус заказа
            if ($closestDriver !== null) {
                $order->driver_id = $closestDriver->id;
                $order->status = 'водитель назначен';
                $order->save();
            }
        }
    }


    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $url = "https://router.project-osrm.org/route/v1/driving/$lon1,$lat1;$lon2,$lat2";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['routes'][0]['distance'])) {
            $distance = $data['routes'][0]['distance'];
            return $distance;
        } else {
            return null;
        }
    }
}
