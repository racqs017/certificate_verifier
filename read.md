/ (your web root or a subdirectory)
├── index.php             (The main upload page)
├── generate.php          (Handles the certificate creation)
├── verify.php            (The public verification page)
├── README.md             (This file)
│
├── certificates/         (NEEDS WRITE PERMISSIONS)
├── qrcodes/              (NEEDS WRITE PERMISSIONS)
├── fonts/                (Optional, for custom fonts)
│   └── Inter-Bold.ttf
│
└── verification_db.csv   (This file will be created automatically)