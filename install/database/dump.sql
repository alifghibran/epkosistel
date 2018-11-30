INSERT INTO `tb_level` (`id`, `level`) VALUES
(1, 'Administrator'),
(2, 'Registrator'),
(3, 'Inspector');

INSERT INTO `tb_panitia` (`id`, `no_induk`, `nama`, `level`, `password`) VALUES
(1, '1234', 'Admin', 1, '81dc9bdb52d04dc20036dbd8313ed055');

INSERT INTO `tb_pengaturan` (`id`, `judul`, `subjudul`, `instansi`, `enable_poll`, `disabled_text`, `timezone`, `default_language`, `v_major`, `v_minor`) VALUES
(1, 'eLection', 'Web Based Election System', 'Tripath Projects', 1, 'Election is closed.', 'default', "en-us", 1, 0);
