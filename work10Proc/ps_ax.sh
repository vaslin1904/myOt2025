#!/bin/bash

# Вывод заголовка таблицы
echo "PID   TTY      STAT    TIME     COMMAND"

# Перебираем все директории в /proc, которые соответствуют PID (числа)
for pid in /proc/[0-9]*; do
    # Извлекаем PID из имени директории
    pid=$(basename "$pid")

    # Проверяем, существует ли файл stat для данного PID
    if [[ -f "/proc/$pid/stat" ]]; then
        # Читаем данные из файла /proc/[PID]/stat
        read -r pid comm state ppid pgrp session tty_nr tpgid flags minflt cminflt majflt cmajflt utime stime cutime cstime priority nice num_threads itrealvalue starttime vsize rss <<< $(cat "/proc/$pid/stat")

        # Преобразуем время выполнения процесса (utime + stime) в человекочитаемый формат
        total_time=$((utime + stime))
        seconds=$((total_time / 100))
        minutes=$((seconds / 60))
        time_formatted=$(printf "%d:%02d" "$minutes" "$((seconds % 60))")

        # Определяем TTY (терминал)
        tty_nr=$((tty_nr))
        if [[ "$tty_nr" -eq 0 ]]; then
            tty="?"
        else
            tty=$(readlink "/proc/$pid/fd/0" 2>/dev/null || echo "?")
        fi

        # Определяем состояние процесса (STAT)
        stat="$state"

        # Читаем команду (COMMAND)
        command=$(cat "/proc/$pid/comm" 2>/dev/null || echo "<unknown>")

        # Выводим информацию в формате ps ax
        printf "%-5s %-8s %-6s %-8s %s\n" "$pid" "$tty" "$stat" "$time_formatted" "$command"
    fi
done
