ALTER TABLE tbsiswa
    ADD COLUMN status_kehadiran VARCHAR(20) NULL DEFAULT NULL AFTER kelas,
    ADD COLUMN terakhir_piket DATETIME NULL DEFAULT NULL AFTER status_kehadiran;
