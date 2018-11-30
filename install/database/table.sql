DROP TABLE IF EXISTS `tb_guru`;
CREATE TABLE `tb_guru` (
  `no_induk` varchar(18) NOT NULL,
  `nama` text NOT NULL,
  `jabatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tb_hakpilih`;
CREATE TABLE `tb_hakpilih` (
  `no_induk` varchar(18) NOT NULL,
  `id_panitia` int(5) NOT NULL,
  `tgl` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kode_akses` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tb_kandidat`;
CREATE TABLE `tb_kandidat` (
`id` int(5) NOT NULL,
  `nama` text NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `bio` text NOT NULL,
  `fbid` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tb_level`;
CREATE TABLE `tb_level` (
`id` int(3) NOT NULL,
  `level` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tb_panitia`;
CREATE TABLE `tb_panitia` (
`id` int(5) NOT NULL,
  `no_induk` varchar(18) NOT NULL,
  `nama` text NOT NULL,
  `level` int(3) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tb_pengaturan`;
CREATE TABLE `tb_pengaturan` (
  `id` int(1) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `subjudul` varchar(200) NOT NULL,
  `instansi` varchar(100) NOT NULL,
  `enable_poll` tinyint(1) NOT NULL,
  `disabled_text` text NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `default_language` varchar(10) NOT NULL,
  `v_major` int(3) NOT NULL,
  `v_minor` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tb_polling`;
CREATE TABLE `tb_polling` (
`id` int(8) NOT NULL,
  `no_induk` varchar(18) NOT NULL,
  `id_panitia` int(5) NOT NULL,
  `tgl` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_kandidat` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tb_siswa`;
CREATE TABLE `tb_siswa` (
  `no_induk` varchar(10) NOT NULL,
  `nama` text NOT NULL,
  `kelas` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
