<?php
/** Services, projects, courses, testimonials, and page titles. */

function content_kinds(): array {
    return [
        'service' => 'Services',
        'project' => 'Projects',
        'course' => 'Courses',
        'testimonial' => 'Testimonials',
    ];
}

function content_defaults(string $kind): array {
    $all = [
        'service' => [
            ['Hardware sales and support', 'Computer sales, assembly, and the accessories an office needs.'],
            ['Repairs and maintenance', 'Diagnostics, repairs, upgrades, and preventive maintenance.'],
            ['Software development', 'Custom software, installation, licensing, and updates.'],
            ['Business automation', 'Systems that take a repeated office task and run it in software.'],
            ['Network installation', 'Network design, Wi-Fi, servers, and ongoing maintenance.'],
            ['IT consultancy', 'Technology planning, system reviews, and infrastructure advice.'],
            ['Equipment installation', 'Setup of the hardware and network equipment you buy.'],
            ['Technology support', 'Help after the installation, for the systems we put in place.'],
        ],
        'project' => [
            ['Software projects', 'Custom systems and the software products listed on this site.'],
            ['Networking', 'Office networks, Wi-Fi, and the equipment that connects them.'],
            ['Installations', 'Hardware and infrastructure set up on site.'],
        ],
        'course' => [
            ['Web Development', 'Full-stack development with modern frameworks'],
            ['Network Engineering', 'CCNA, CCNP and enterprise networking'],
            ['Cybersecurity', 'Ethical hacking and security analysis'],
            ['Hardware Repair', 'Computer technician training'],
            ['Data Science', 'Analytics, machine learning, and AI fundamentals'],
            ['Cloud Computing', 'AWS, Azure, and Google Cloud'],
        ],
        'testimonial' => [],
    ];
    return $all[$kind] ?? [];
}

function seo_defaults(): array {
    return [
        'home' => ['GITSYSTEM - Greenpasture Integrated Technology', 'Professional IT solutions, hardware/software services, networking, tech academy, and consultation in Nigeria. Your trusted technology partner.'],
        'about' => ['About', 'GreenPasture Integrated Tech System supplies hardware, builds software, installs networks, and trains teams.'],
        'services' => ['Services', 'Hardware, repairs, software, business automation, networking, consultancy, installation, and technology support.'],
        'products' => ['Products', 'Browse GITSystem products. Each item has details, pricing, and an enquiry button.'],
        'software' => ['Software', 'Software products published from the GITSystem catalog, plus TailorMate.'],
        'academy' => ['GIT Academy', 'Training programmes from GITSystem. Enrol by sending an enquiry.'],
        'projects' => ['Projects', 'The kinds of technology work GITSystem does. Individual project write-ups are not published yet.'],
        'contact' => ['Contact', 'Contact GITSystem for a consultation or an enrolment enquiry.'],
    ];
}

function content_ensure(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS content_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kind ENUM('service','project','course','testimonial') NOT NULL,
        title VARCHAR(180) NOT NULL,
        body TEXT NOT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_kind_sort (kind, sort_order, id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS page_seo (
        page_key VARCHAR(40) PRIMARY KEY,
        title VARCHAR(180) NOT NULL,
        description VARCHAR(300) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if ((int)$pdo->query('SELECT COUNT(*) FROM content_items')->fetchColumn() === 0) {
        $stmt = $pdo->prepare('INSERT INTO content_items (kind, title, body, sort_order) VALUES (?, ?, ?, ?)');
        foreach (content_kinds() as $kind => $label) {
            $n = 1;
            foreach (content_defaults($kind) as [$title, $body]) {
                $stmt->execute([$kind, $title, $body, $n++]);
            }
        }
    }

    if ((int)$pdo->query('SELECT COUNT(*) FROM page_seo')->fetchColumn() === 0) {
        $stmt = $pdo->prepare('INSERT INTO page_seo (page_key, title, description) VALUES (?, ?, ?)');
        foreach (seo_defaults() as $key => [$title, $description]) {
            $stmt->execute([$key, $title, $description]);
        }
    }
}

function content_public(PDO $pdo, string $kind): ?array {
    try {
        $stmt = $pdo->prepare('SELECT title, body FROM content_items WHERE kind = ? AND is_active = 1 ORDER BY sort_order, id');
        $stmt->execute([$kind]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        try {
            content_ensure($pdo);
            $stmt = $pdo->prepare('SELECT title, body FROM content_items WHERE kind = ? AND is_active = 1 ORDER BY sort_order, id');
            $stmt->execute([$kind]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return null;
        }
    }
}

function content_or_default(PDO $pdo, string $kind): array {
    $rows = content_public($pdo, $kind);
    if ($rows === null) {
        return array_map(fn($pair) => ['title' => $pair[0], 'body' => $pair[1]], content_defaults($kind));
    }
    return $rows;
}

function seo_public(PDO $pdo, string $key): ?array {
    try {
        $stmt = $pdo->prepare('SELECT title, description FROM page_seo WHERE page_key = ?');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (PDOException $e) {
        try {
            content_ensure($pdo);
            $stmt = $pdo->prepare('SELECT title, description FROM page_seo WHERE page_key = ?');
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
