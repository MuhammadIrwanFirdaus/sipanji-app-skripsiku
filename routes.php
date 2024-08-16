<?php
if (isset($_GET['page'])){
    $page = $_GET['page'];
    switch ($page) {
        case '':
        case 'home':
            if (file_exists('pages/home.php')) {
                include 'pages/home.php';
            } else {
                include 'pages/404.php';
            }
            break;
            case 'login-view':
                if (file_exists('pages/login/view.php')) {
                    include 'pages/login/view.php';
                } else {
                    include 'pages/404.php';
                }
                break;
                case 'tambah-admin':
                    if (file_exists('pages/login/tambah.php')) {
                        include 'pages/login/tambah.php';
                    } else {
                        include 'pages/404.php';
                    }
                    break;
                    case 'edit-admin':
                        if (file_exists('pages/login/edit.php')) {
                            include 'pages/login/edit.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                        case 'hapus-admin':
                            if (file_exists('pages/login/hapus.php')) {
                                include 'pages/login/hapus.php';
                            } else {
                                include 'pages/404.php';
                            }
                            break;
            case 'download-pengajuan':
                if (file_exists('pages/data-pengajuan/cetak-pdf.php')) {
                    include 'pages/data-pengajuan/cetak-pdf.php';
                } else {
                    include 'pages/404.php';
                }
                break;
                case 'tampil-admin':
                    if (file_exists('pages/login/tampil.php')) {
                        include 'pages/login/tampil.php';
                    } else {
                        include 'pages/404.php';
                    }
                    break;
                case 'cetak-data-stok':
                    if (file_exists('pages/stok-alat/cetak-pdf-stok.php')) {
                        include 'pages/stok-alat/cetak-pdf-stok.php';
                    } else {
                        include 'pages/404.php';
                    }
                    break;
            case 'cetak-pdf-gabungan':
                if (file_exists('pages/Pengerjaan/cetak-pdf-gabungan.php')) {
                    include 'pages/Pengerjaan/cetak-pdf-gabungan.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'cetak-data-pengajuan':
                if (file_exists('pages/data-pengajuan/view-pdf.php')) {
                    include 'pages/data-pengajuan/view-pdf.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'Jadwal':
                if (file_exists('jadwal.php')) {
                    include 'jadwal.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'tambahJadwal':
                if (file_exists('pages/kalender/tambah.php')) {
                    include 'pages/kalender/tambah.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'detailJadwal':
                if (file_exists('pages/kalender/detail.php')) {
                    include 'pages/kalender/detail.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'editJadwal':
                if (file_exists('pages/kalender/edit.php')) {
                    include 'pages/kalender/edit.php';
                } else {
                    include 'pages/404.php';
                }
                break;
                case 'view-pdf-pemasangan':
                    if (file_exists('pages/kalender/view-pdf-pemasangan.php')) {
                        include 'pages/kalender/view-pdf-pemasangan.php';
                    } else {
                        include 'pages/404.php';
                    }
                    break;
                case 'view-pdf-survey':
                    if (file_exists('pages/kalender/view-pdf-survey.php')) {
                        include 'pages/kalender/view-pdf-survey.php';
                    } else {
                        include 'pages/404.php';
                    }
                    break;
                    case 'view-pdf-gabungan':
                        if (file_exists('pages/Pengerjaan/view-pdf-gabungan.php')) {
                            include 'pages/Pengerjaan/view-pdf-gabungan.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
            case 'cetak-pdf-survey':
                if (file_exists('pages/kalender/cetak-pdf-survey.php')) {
                    include 'pages/kalender/cetak-pdf-survey.php';
                } else {
                    include 'pages/404.php';
                }
                break;
                case 'cetak-pdf-pemasangan':
                    if (file_exists('pages/kalender/cetak-pdf-pemasangan.php')) {
                        include 'pages/kalender/cetak-pdf-pemasangan.php';
                    } else {
                        include 'pages/404.php';
                    }
                    break;
            case 'tampil-stok-alat':
                if (file_exists('pages/stok-alat/tampil.php')) {
                    include 'pages/stok-alat/tampil.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'tambah-stok-alat':
                if (file_exists('pages/stok-alat/tambah.php')) {
                    include 'pages/stok-alat/tambah.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'update-stok-alat':
                if (file_exists('pages/stok-alat/tambah-stok.php')) {
                    include 'pages/stok-alat/tambah-stok.php';
                } else {
                    include 'pages/404.php';
                }
                break;
            case 'hapus-stok-alat':
                if (file_exists('pages/stok-alat/hapus.php')) {
                    include 'pages/stok-alat/hapus.php';
                } else {
                        include 'pages/404.php';
                }
                break;
            case 'ubah-stok-alat':
                if (file_exists('pages/stok-alat/edit.php')) {
                    include 'pages/stok-alat/edit.php';
                } else {
                        include 'pages/404.php';
                }
                break;
                case 'upload-pengerjaan':
                    if (file_exists('pages/pengerjaan/tambah.php')) {
                        include 'pages/pengerjaan/tambah.php';
                    } else {
                            include 'pages/404.php';
                    }
                    break;
                    case 'tampil-pengerjaan':
                        if (file_exists('pages/pengerjaan/tampil.php')) {
                            include 'pages/pengerjaan/tampil.php';
                        } else {
                                include 'pages/404.php';
                        }
                        break;
                        case 'edit-data-pengerjaan':
                            if (file_exists('pages/pengerjaan/edit.php')) {
                                include 'pages/pengerjaan/edit.php';
                            } else {
                                    include 'pages/404.php';
                            }
                            break;
                        case 'hapus-data-pengerjaan':
                            if (file_exists('pages/pengerjaan/hapus.php')) {
                                include 'pages/pengerjaan/hapus.php';
                            } else {
                                    include 'pages/404.php';
                            }
                            break;
                    case 'tampil-data-instansi':
                        if (file_exists('pages/data-instansi/tampil.php')) {
                            include 'pages/data-instansi/tampil.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'tambah-data-instansi':
                        if (file_exists('pages/data-instansi/tambah.php')) {
                            include 'pages/data-instansi/tambah.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'detail-data-instansi':
                        if (file_exists('pages/data-instansi/detail.php')) {
                            include 'pages/data-instansi/detail.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'edit-data-instansi':
                        if (file_exists('pages/data-instansi/edit.php')) {
                            include 'pages/data-instansi/edit.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'hapus-data-instansi':
                        if (file_exists('pages/data-instansi/hapus.php')) {
                            include 'pages/data-instansi/hapus.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'tampil-pengajuan':
                        if (file_exists('pages/data-pengajuan/tampil.php')) {
                            include 'pages/data-pengajuan/tampil.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'tambah-data-pengajuan':
                        if (file_exists('pages/data-pengajuan/tambah.php')) {
                            include 'pages/data-pengajuan/tambah.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'edit-data-pengajuan':
                        if (file_exists('pages/data-pengajuan/edit.php')) {
                            include 'pages/data-pengajuan/edit.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'detail-data-pengajuan':
                        if (file_exists('pages/data-pengajuan/detail.php')) {
                            include 'pages/data-pengajuan/detail.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                    case 'hapus-data-pengajuan':
                        if (file_exists('pages/data-pengajuan/hapus.php')) {
                            include 'pages/data-pengajuan/hapus.php';
                        } else {
                            include 'pages/404.php';
                        }
                        break;
                        case 'halaman-admin':
                            if (file_exists('pages/beranda/admin.php')) {
                                include 'pages/beranda/admin.php';
                            } else {
                                include 'pages/404.php';
                            }
                            break;
                            case 'halaman-kominfo':
                                if (file_exists('pages/beranda/kominfo.php')) {
                                    include 'pages/beranda/kominfo.php';
                                } else {
                                    include 'pages/404.php';
                                }
                                break;
                                case 'tambah-pengajuan-instansi':
                                    if (file_exists('pages/data-pengajuan/tambah-instansi.php')) {
                                        include 'pages/data-pengajuan/tambah-instansi.php';
                                    } else {
                                        include 'pages/404.php';
                                    }
                                    break;
                                case 'tambah-pengajuan-umum':
                                    if (file_exists('pages/data-pengajuan/tambah-umum.php')) {
                                        include 'pages/data-pengajuan/tambah-umum.php';
                                    } else {
                                        include 'pages/404.php';
                                    }
                                    break;
                                    case 'halaman-instansi':
                                        if (file_exists('pages/beranda/instansi.php')) {
                                            include 'pages/beranda/instansi.php';
                                        } else {
                                            include 'pages/404.php';
                                        }
                                        break;
                                        case 'halaman-umum':
                                            if (file_exists('pages/beranda/umum.php')) {
                                                include 'pages/beranda/umum.php';
                                            } else {
                                                include 'pages/404.php';
                                            }
                                            break;
                                        case 'monitoring-pengajuan-instansi':
                                            if (file_exists('pages/data-pengajuan/monitoring-pengajuan.php')) {
                                                include 'pages/data-pengajuan/monitoring-pengajuan.php';
                                            } else {
                                                include 'pages/404.php';
                                            }
                                            break;
                                            case 'monitoring-pengajuan-umum':
                                                if (file_exists('pages/data-pengajuan/monitoring-pengajuan-umum.php')) {
                                                    include 'pages/data-pengajuan/monitoring-pengajuan-umum.php';
                                                } else {
                                                    include 'pages/404.php';
                                                }
                                                break;
                                            case 'kelola-pengajuan':
                                                if (file_exists('pages/data-pengajuan/kelola-pengajuan.php')) {
                                                    include 'pages/data-pengajuan/kelola-pengajuan.php';
                                                } else {
                                                    include 'pages/404.php';
                                                }
                                                break;
                                                case 'kelola-pengguna':
                                                    if (file_exists('pages/login/kelola_login.php')) {
                                                        include 'pages/login/kelola_login.php';
                                                    } else {
                                                        include 'pages/404.php';
                                                    }
                                                    break;
                                                    case 'surat-tugas':
                                                        if (file_exists('pages/kalender/surat-tugas.php')) {
                                                            include 'pages/kalender/surat-tugas.php';
                                                        } else {
                                                            include 'pages/404.php';
                                                        }
                                                        break;
                                                    case 'statistik-pengguna':
                                                        if (file_exists('pages/login/statistik_pengguna.php')) {
                                                            include 'pages/login/statistik_pengguna.php';
                                                        } else {
                                                            include 'pages/404.php';
                                                        }
                                                        break;
                                                    case 'monitoring-alat':
                                                        if (file_exists('pages/pemeliharaan/tampil.php')) {
                                                            include 'pages/pemeliharaan/tampil.php';
                                                        } else {
                                                            include 'pages/404.php';
                                                        }
                                                        break;
                                                        case 'tambah-alat':
                                                            if (file_exists('pages/pemeliharaan/alat.php')) {
                                                                include 'pages/pemeliharaan/alat.php';
                                                            } else {
                                                                include 'pages/404.php';
                                                            }
                                                            break;
                                                            case 'serah-terima':
                                                                if (file_exists('pages/pengerjaan/serah-terima.php')) {
                                                                    include 'pages/pengerjaan/serah-terima.php';
                                                                } else {
                                                                    include 'pages/404.php';
                                                                }
                                                                break;
                                                                case 'cetak-serah-terima':
                                                                    if (file_exists('pages/pengerjaan/cetak-serah.php')) {
                                                                        include 'pages/pengerjaan/cetak-serah.php';
                                                                    } else {
                                                                        include 'pages/404.php';
                                                                    }
                                                                    break;
                                                                    case 'view-biaya-operasional':
                                                                        if (file_exists('pages/pengerjaan/view_biaya_operasional.php')) {
                                                                            include 'pages/pengerjaan/view_biaya_operasional.php';
                                                                        } else {
                                                                                include 'pages/404.php';
                                                                        }
                                                                        break;
                                                                        case 'cetak-pdf-biaya-operasional':
                                                                            if (file_exists('pages/pengerjaan/cetak_pdf_biaya_operasional.php')) {
                                                                                include 'pages/pengerjaan/cetak_pdf_biaya_operasional.php';
                                                                            } else {
                                                                                    include 'pages/404.php';
                                                                            }
                                                                            break;
                                                                            case 'cetak-pdf-pelayanan':
                                                                                if (file_exists('pages/kepuasan-pelayanan/cetak-pdf-pelayanan.php')) {
                                                                                    include 'pages/kepuasan-pelayanan/cetak-pdf-pelayanan.php';
                                                                                } else {
                                                                                        include 'pages/404.php';
                                                                                }
                                                                                break;
                                                                                case 'tambah-gangguan':
                                                                                    if (file_exists('pages/gangguan/tambah.php')) {
                                                                                        include 'pages/gangguan/tambah.php';
                                                                                    } else {
                                                                                            include 'pages/404.php';
                                                                                    }
                                                                                    break;
                                                                                    case 'tampil-gangguan':
                                                                                        if (file_exists('pages/gangguan/tampil.php')) {
                                                                                            include 'pages/gangguan/tampil.php';
                                                                                        } else {
                                                                                                include 'pages/404.php';
                                                                                        }
                                                                                        break;
                                                                                    case 'edit-gangguan':
                                                                                        if (file_exists('pages/gangguan/edit.php')) {
                                                                                            include 'pages/gangguan/edit.php';
                                                                                        } else {
                                                                                                include 'pages/404.php';
                                                                                        }
                                                                                        break;
                                                                                        case 'hapus-gangguan':
                                                                                            if (file_exists('pages/gangguan/hapus.php')) {
                                                                                                include 'pages/gangguan/hapus.php';
                                                                                            } else {
                                                                                                    include 'pages/404.php';
                                                                                            }
                                                                                            break;
                                                                                            case 'edit-alat':
                                                                                                if (file_exists('pages/pemeliharaan/edit.php')) {
                                                                                                    include 'pages/pemeliharaan/edit.php';
                                                                                                } else {
                                                                                                        include 'pages/404.php';
                                                                                                }
                                                                                                break;
                                                                                                case 'hapus-alat':
                                                                                                    if (file_exists('pages/pemeliharaan/hapus.php')) {
                                                                                                        include 'pages/pemeliharaan/hapus.php';
                                                                                                    } else {
                                                                                                            include 'pages/404.php';
                                                                                                    }
                                                                                                    break;
                                                                                                    case 'tampil-peta':
                                                                                                        if (file_exists('pages/pemeliharaan/tampil-peta.php')) {
                                                                                                            include 'pages/pemeliharaan/tampil-peta.php';
                                                                                                        } else {
                                                                                                                include 'pages/404.php';
                                                                                                        }
                                                                                                        break;
                                                                                                        case 'view-jadwal':
                                                                                                            if (file_exists('pages/kalender/view-jadwal.php')) {
                                                                                                                include 'pages/kalender/view-jadwal.php';
                                                                                                            } else {
                                                                                                                    include 'pages/404.php';
                                                                                                            }
                                                                                                            break;
                                                                                                            case 'download-jadwal':
                                                                                                                if (file_exists('pages/kalender/cetak-pdf.php')) {
                                                                                                                    include 'pages/kalender/cetak-pdf.php';
                                                                                                                } else {
                                                                                                                        include 'pages/404.php';
                                                                                                                }
                                                                                                                break;
                                                                                                            case 'cetak-pemeliharaan':
                                                                                                            if (file_exists('pages/pemeliharaan/cetak-pdf.php')) {
                                                                                                            include 'pages/pemeliharaan/cetak-pdf.php';
                                                                                                                } else {
                                                                                                                    include 'pages/404.php';
                                                                                                            }
                                                                                                            break;
                                                                                                                case 'edit-profil':
                                                                                                                    if (file_exists('pages/login/edit_profil.php')) {
                                                                                                                        include 'pages/login/edit_profil.php';
                                                                                                                    } else {
                                                                                                                            include 'pages/404.php';
                                                                                                                    }
                                                                                                                    break;
                                                                                                                    case 'view-gangguan-pdf':
                                                                                                                        if (file_exists('pages/gangguan/view-pdf.php')) {
                                                                                                                            include 'pages/gangguan/view-pdf.php';
                                                                                                                        } else {
                                                                                                                                include 'pages/404.php';
                                                                                                                        }
                                                                                                                        break;
                                                                                                                        case 'download-gangguan':
                                                                                                                            if (file_exists('pages/gangguan/cetak-pdf.php')) {
                                                                                                                                include 'pages/gangguan/cetak-pdf.php';
                                                                                                                            } else {
                                                                                                                                    include 'pages/404.php';
                                                                                                                            }
                                                                                                                            break;
                                                                                                                            case 'pdf-pengguna':
                                                                                                                                if (file_exists('pages/login/pdf-pengguna.php')) {
                                                                                                                                    include 'pages/login/pdf-pengguna.php';
                                                                                                                                } else {
                                                                                                                                        include 'pages/404.php';
                                                                                                                                }
                                                                                                                                break;
                                                                                                                                case 'pdf-alat-sering':
                                                                                                                                    if (file_exists('pages/pengerjaan/cetak-pdf-alat-sering.php')) {
                                                                                                                                        include 'pages/pengerjaan/cetak-pdf-alat-sering.php';
                                                                                                                                    } else {
                                                                                                                                            include 'pages/404.php';
                                                                                                                                    }
                                                                                                                                    break;
                default:
            include 'pages/404.php';
    }
} else {
    include 'pages/home.php';
}