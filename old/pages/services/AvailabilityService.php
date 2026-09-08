<?php

class AvailabilityService
{
    /**
     * Боломжит цагуудыг гаргах
     *
     * @param int    $doctorId
     * @param int    $serviceId
     * @param string $date       Y-m-d
     *
     * @return array
     */
    public static function getAvailableSlots(
        int $doctorId,
        int $serviceId,
        string $date
    ): array {

        /*
        |--------------------------------------------------------------------------
        | 1. Үйлчилгээний хугацаа
        |--------------------------------------------------------------------------
        */

        $durationMinutes = null;

        _selectRow(
            "
                SELECT duration_minutes
                FROM services
                WHERE id = ?
                AND is_active = 1
                LIMIT 1
            ",
            "i",
            [$serviceId],
            $durationMinutes
        );

        if ($durationMinutes === null) {
            return [];
        }

        $durationMinutes = (int)$durationMinutes;


        /*
        |--------------------------------------------------------------------------
        | 2. Долоо хоногийн өдөр
        |--------------------------------------------------------------------------
        |
        | PHP date('N'):
        |
        | 1 = Даваа
        | 2 = Мягмар
        | 3 = Лхагва
        | 4 = Пүрэв
        | 5 = Баасан
        | 6 = Бямба
        | 7 = Ням
        |
        */

        $dayOfWeek = (int)date(
            'N',
            strtotime($date)
        );


        /*
        |--------------------------------------------------------------------------
        | 3. Эмч тухайн өдөр хэдэн schedule-тэй вэ?
        |--------------------------------------------------------------------------
        */

        _select(
            $stmt,
            $count,
            "
                SELECT
                    id,
                    start_time,
                    end_time,
                    slot_interval_minutes
                FROM doctor_schedules
                WHERE doctor_id = ?
                AND day_of_week = ?
                AND is_active = 1
                ORDER BY start_time
            ",
            "ii",
            [$doctorId, $dayOfWeek],
            $scheduleId,
            $scheduleStartTime,
            $scheduleEndTime,
            $slotInterval
        );


        /*
        |--------------------------------------------------------------------------
        | 4. Тухайн өдрийн захиалгууд
        |--------------------------------------------------------------------------
        */

        $appointments = self::getAppointments(
            $doctorId,
            $date
        );


        /*
        |--------------------------------------------------------------------------
        | 5. Боломжит цагууд
        |--------------------------------------------------------------------------
        */

        $availableSlots = [];


        while (_fetch($stmt)) {

            $scheduleId = (int)$scheduleId;

            $slotInterval = (int)$slotInterval;


            /*
            |--------------------------------------------------------------------------
            | Schedule-ийн эхлэх / дуусах timestamp
            |--------------------------------------------------------------------------
            */

            $scheduleStart = strtotime(
                $date . ' ' . $scheduleStartTime
            );

            $scheduleEnd = strtotime(
                $date . ' ' . $scheduleEndTime
            );


            /*
            |--------------------------------------------------------------------------
            | Slot interval буруу байвал
            |--------------------------------------------------------------------------
            */

            if ($slotInterval <= 0) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | 6. Цайны цаг
            |--------------------------------------------------------------------------
            */

            $breaks = self::getBreaks(
                $scheduleId,
                $date
            );


            /*
            |--------------------------------------------------------------------------
            | 7. Slot үүсгэх
            |--------------------------------------------------------------------------
            */

            $intervalSeconds =
                $slotInterval * 60;


            for (
                $slotStart = $scheduleStart;
                $slotStart < $scheduleEnd;
                $slotStart += $intervalSeconds
            ) {

                /*
                |--------------------------------------------------------------------------
                | Үзлэг дуусах цаг
                |--------------------------------------------------------------------------
                */

                $slotEnd =
                    $slotStart
                    + ($durationMinutes * 60);


                /*
                |--------------------------------------------------------------------------
                | Ажлын цаг дууссанаас хэтэрсэн бол
                |--------------------------------------------------------------------------
                */

                if ($slotEnd > $scheduleEnd) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Цайны цагтай давхцаж байна уу?
                |--------------------------------------------------------------------------
                */

                if (
                    self::hasConflict(
                        $slotStart,
                        $slotEnd,
                        $breaks
                    )
                ) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Өмнөх захиалгатай давхцаж байна уу?
                |--------------------------------------------------------------------------
                */

                if (
                    self::hasConflict(
                        $slotStart,
                        $slotEnd,
                        $appointments
                    )
                ) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Боломжит цаг
                |--------------------------------------------------------------------------
                */

                $availableSlots[] = [
                    'start_time' => date(
                        'H:i',
                        $slotStart
                    ),

                    'end_time' => date(
                        'H:i',
                        $slotEnd
                    )
                ];
            }
        }


        _close_stmt($stmt);


        return $availableSlots;
    }


    /**
     * Тухайн эмчийн тухайн өдрийн захиалгууд
     */
    private static function getAppointments(
        int $doctorId,
        string $date
    ): array {

        _select(
            $stmt,
            $count,
            "
                SELECT
                    start_time,
                    duration_minutes
                FROM appointments
                WHERE doctor_id = ?
                AND appointment_date = ?
                AND status IN ('BOOKED', 'CONFIRMED')
                ORDER BY start_time
            ",
            "is",
            [$doctorId, $date],
            $startTime,
            $durationMinutes
        );


        $appointments = [];


        while (_fetch($stmt)) {

            $start = strtotime(
                $date . ' ' . $startTime
            );

            $end =
                $start
                + (
                    (int)$durationMinutes
                    * 60
                );


            $appointments[] = [
                'start' => $start,
                'end'   => $end
            ];
        }


        _close_stmt($stmt);


        return $appointments;
    }


    /**
     * Schedule-ийн цайны / завсарлагын цагууд
     */
    private static function getBreaks(
        int $scheduleId,
        string $date
    ): array {

        _select(
            $stmt,
            $count,
            "
                SELECT
                    start_time,
                    end_time
                FROM doctor_breaks
                WHERE doctor_schedule_id = ?
                ORDER BY start_time
            ",
            "i",
            [$scheduleId],
            $startTime,
            $endTime
        );


        $breaks = [];


        while (_fetch($stmt)) {

            $breaks[] = [
                'start' => strtotime(
                    $date . ' ' . $startTime
                ),

                'end' => strtotime(
                    $date . ' ' . $endTime
                )
            ];
        }


        _close_stmt($stmt);


        return $breaks;
    }


    /**
     * Цагийн интервал бусад интервалтай давхцаж байна уу?
     */
    private static function hasConflict(
        int $start,
        int $end,
        array $intervals
    ): bool {

        foreach ($intervals as $interval) {

            /*
            |--------------------------------------------------------------------------
            | Давхцлын нөхцөл
            |--------------------------------------------------------------------------
            |
            | start < other_end
            | &&
            | end > other_start
            |
            */

            if (
                $start < $interval['end']
                &&
                $end > $interval['start']
            ) {
                return true;
            }
        }


        return false;
    }
}