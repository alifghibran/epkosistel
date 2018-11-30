ALTER TABLE `tb_guru`
 ADD PRIMARY KEY (`no_induk`), ADD UNIQUE KEY `no_induk` (`no_induk`);

ALTER TABLE `tb_hakpilih`
 ADD PRIMARY KEY (`no_induk`), ADD UNIQUE KEY `kode_akses` (`kode_akses`);

ALTER TABLE `tb_kandidat`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `tb_level`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `tb_panitia`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `no_induk` (`no_induk`);

ALTER TABLE `tb_pengaturan`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `tb_polling`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `tb_siswa`
 ADD PRIMARY KEY (`no_induk`), ADD UNIQUE KEY `no_induk` (`no_induk`);


ALTER TABLE `tb_kandidat`
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_level`
MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_panitia`
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_polling`
MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;
