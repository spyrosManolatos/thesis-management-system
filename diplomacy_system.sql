-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 11:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `diplomacy_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `additional_student_material`
--

CREATE TABLE `additional_student_material` (
  `add_st_material_id` int(11) NOT NULL,
  `st_material_link` varchar(500) NOT NULL,
  `thesis_assignment_id` int(11) NOT NULL,
  `description` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `additional_student_material`
--

INSERT INTO `additional_student_material` (`add_st_material_id`, `st_material_link`, `thesis_assignment_id`, `description`) VALUES
(2, 'https://ceid.upatras.gr', 12, 'Υλικο1');

-- --------------------------------------------------------

--
-- Table structure for table `assembly_decisions`
--

CREATE TABLE `assembly_decisions` (
  `desicion_id` int(11) NOT NULL,
  `assembly_year` int(11) NOT NULL,
  `assembly_number` int(11) NOT NULL,
  `assembly_decision` varchar(500) NOT NULL,
  `thesis_assignment_id` int(11) NOT NULL,
  `secretary_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assembly_decisions`
--

INSERT INTO `assembly_decisions` (`desicion_id`, `assembly_year`, `assembly_number`, `assembly_decision`, `thesis_assignment_id`, `secretary_id`) VALUES
(5, 2025, 13, 'ΑΚΥΡΩΣΗ ΘΕΜΑΤΟΣ ΛΟΓΩ ΦΟΙΤΗΤΗ', 29, 5),
(6, 2025, 13, 'ΕΠΙΣΗΜΗ ΑΝΑΘΕΣΗ ΘΕΜΑΤΟΣ', 7, 5),
(7, 2025, 13, 'ΕΠΙΣΗΜΗ ΑΝΑΘΕΣΗ ΘΕΜΑΤΟΣ', 30, 5);

-- --------------------------------------------------------

--
-- Table structure for table `committee`
--

CREATE TABLE `committee` (
  `com_id` int(11) NOT NULL,
  `avg_mark` int(11) DEFAULT NULL,
  `thesis_assignment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `committee`
--

INSERT INTO `committee` (`com_id`, `avg_mark`, `thesis_assignment_id`) VALUES
(1, NULL, 7),
(2, 8, 8),
(3, 9, 9),
(4, NULL, 11),
(5, NULL, 12),
(6, NULL, 16),
(7, NULL, 29),
(8, 9, 30);

-- --------------------------------------------------------

--
-- Table structure for table `committee_invitations`
--

CREATE TABLE `committee_invitations` (
  `invitation_id` int(11) NOT NULL,
  `invitation_date` date DEFAULT curdate(),
  `professor_id` int(11) NOT NULL,
  `response_date` date DEFAULT NULL,
  `status` enum('invited','accepted','rejected') NOT NULL,
  `thesis_assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `committee_invitations`
--

INSERT INTO `committee_invitations` (`invitation_id`, `invitation_date`, `professor_id`, `response_date`, `status`, `thesis_assignment_id`) VALUES
(2, '2025-03-04', 1, '2025-03-05', 'accepted', 7),
(4, '2025-03-05', 3, '2025-03-05', 'accepted', 7),
(6, '2025-03-05', 2, '2025-03-05', 'accepted', 8),
(8, '2025-03-05', 1, '2025-03-05', 'accepted', 8),
(9, '2025-03-06', 4, '2025-03-06', 'accepted', 9),
(10, '2025-03-06', 2, '2025-03-06', 'accepted', 9),
(11, '2025-03-07', 2, '2025-03-07', 'rejected', 10),
(12, '2025-03-19', 3, '2025-03-19', 'accepted', 11),
(13, '2025-03-19', 5, '2025-03-19', 'accepted', 11),
(14, '2025-03-23', 6, '2025-03-23', 'accepted', 12),
(15, '2025-03-25', 2, '2025-03-23', 'accepted', 12),
(16, '2025-04-17', 5, '2025-04-17', 'rejected', 6),
(17, '2025-04-21', 4, '2025-04-21', 'accepted', 16),
(18, '2025-04-21', 5, '2025-04-21', 'accepted', 16),
(20, '2025-05-31', 2, '2025-05-31', 'accepted', 6),
(21, '2025-05-31', 3, '2025-06-02', 'rejected', 6),
(30, '2025-06-02', 4, '2025-06-02', 'accepted', 29),
(31, '2025-06-02', 5, '2025-06-02', 'accepted', 29),
(34, '2025-06-05', 4, '2025-06-05', 'accepted', 30),
(35, '2025-06-05', 5, '2025-06-05', 'accepted', 30);

-- --------------------------------------------------------

--
-- Table structure for table `committee_members`
--

CREATE TABLE `committee_members` (
  `com_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `is_supervisor` tinyint(1) NOT NULL DEFAULT 0,
  `mark_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `committee_members`
--

INSERT INTO `committee_members` (`com_id`, `teacher_id`, `is_supervisor`, `mark_id`) VALUES
(1, 1, 0, NULL),
(1, 2, 1, NULL),
(1, 3, 0, NULL),
(2, 1, 0, 12),
(2, 2, 0, 17),
(2, 3, 1, 11),
(3, 2, 0, 4),
(3, 4, 0, 3),
(3, 5, 1, 8),
(4, 3, 0, NULL),
(4, 5, 0, NULL),
(4, 6, 1, NULL),
(5, 2, 0, NULL),
(5, 5, 1, 10),
(5, 6, 0, NULL),
(6, 1, 1, NULL),
(6, 4, 0, NULL),
(6, 5, 0, NULL),
(7, 1, 1, NULL),
(7, 4, 0, NULL),
(7, 5, 0, NULL),
(8, 1, 1, 18),
(8, 4, 0, 19),
(8, 5, 0, 20);

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `mark_id` int(11) NOT NULL,
  `targets_fulfiled` float NOT NULL,
  `quality_completeness` float NOT NULL,
  `readable_thesis` float NOT NULL,
  `time_satisfied` float NOT NULL DEFAULT 10,
  `final_mark` float GENERATED ALWAYS AS (0.6 * `targets_fulfiled` + 0.15 * `time_satisfied` + 0.15 * `quality_completeness` + 0.1 * `readable_thesis`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`mark_id`, `targets_fulfiled`, `quality_completeness`, `readable_thesis`, `time_satisfied`) VALUES
(3, 8.5, 9, 10, 10),
(4, 10, 7.5, 9, 10),
(8, 10, 10, 10, 10),
(10, 10, 10, 7.5, 10),
(11, 5, 5, 5, 10),
(12, 9, 8, 7, 10),
(16, 10, 10, 9, 10),
(17, 10, 10, 10, 10),
(18, 8, 9, 10, 10),
(19, 10, 9, 10, 10),
(20, 10, 10, 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `professor_notes`
--

CREATE TABLE `professor_notes` (
  `prof_note` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `note_content` text DEFAULT NULL,
  `date_created` date DEFAULT curdate(),
  `professor_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professor_notes`
--

INSERT INTO `professor_notes` (`prof_note`, `title`, `note_content`, `date_created`, `professor_id`, `assignment_id`) VALUES
(1, 'σημειωση', 'γεια σας', '2025-03-05', 2, 7),
(2, 'σημειωση1', 'Καλησπέρα σας', '2025-03-07', 1, 7),
(3, 'σημείωση2', 'τι είναι αυτό;', '2025-03-07', 1, 7),
(4, 'σημειωση2', 'γειαααα', '2025-03-23', 2, 7),
(5, 'kasokosdakodsak', 'dsaokdasosakoaskd', '2025-03-27', 1, 7),
(6, 'simeiosi10', 'Validate your email\r\nHi Spyros Manolatos,\r\n\r\nThank you for creating an xAI account. Please click the link below to validate your email address.\r\n\r\nValidate my email address\r\n\r\nIf this link does not work, navigate to this URL in your browser:\r\n\r\nhttps://accounts.x.ai/verify-email?user-id=36e520e5-ba95-456b-92c2-cc7ca65c751d&csrf=6urHMTsiH8PA9cRe46ESfFvUZonhdYQ5lM7gGQgO7fHKgRIH\r\n\r\nIf you did not create a new account, please ignore this email and don\'t tap the link above.\r\n\r\nSo long, and thanks for all the fish,\r\nThe xAI Team', '2025-03-27', 1, 7),
(7, 'shmeiosi15', 'simeiosi15', '2025-03-28', 1, 7),
(12, 'sex15', '<p>6969696969</p>', '2025-03-28', 1, 7),
(13, 'sex15', '<p><strong>6969696969</strong></p>', '2025-03-28', 1, 7),
(14, 'kalimera ', '<p><strong>xjsijsiaji</strong></p>', '2025-04-06', 1, 7),
(15, 'einai to mellon', '<p><u>kalispera</u></p>', '2025-04-07', 1, 7),
(16, 'einai to mellon', '<p><u>kalispera</u></p>', '2025-04-07', 1, 7),
(17, 'KALIMERA', '<p>kalimeraaaaa</p>', '2025-04-07', 1, 7),
(18, 'geia sas', '<p>oraios</p>', '2025-04-07', 1, 7),
(19, 'simeiosi', '<p><strong>edo einai mia <u>simiosi</u></strong></p>', '2025-04-16', 1, 7),
(20, 'σημείωση10', '<ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>σεξ</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>οκ</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>είναι</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>ωραίο</li></ol>', '2025-04-16', 2, 7),
(21, 'hey', '<p><strong>notes</strong></p>', '2025-04-22', 1, 16),
(22, 'Σημείωση1', '<p><strong>Σημείωση</strong></p>', '2025-05-13', 1, 16),
(23, 'kalisperma', '<p><strong>sexoualiko</strong></p>', '2025-05-21', 1, 16),
(24, 'simeiosi2', '<p><strong>simeiosi</strong></p>', '2025-05-21', 1, 7),
(25, 'Σημείωση10', '<p><em>Σημείωση</em></p>', '2025-05-29', 1, 16),
(26, 'Κατι', '<p><strong>κατι αλλο</strong></p>', '2025-06-02', 1, 29),
(27, 'Σημείωση1-Κείμενο', '<p><em><u>Κείμενο</u></em></p>', '2025-06-05', 1, 30);

-- --------------------------------------------------------

--
-- Table structure for table `secrertary`
--

CREATE TABLE `secrertary` (
  `secrertary_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `secrertary`
--

INSERT INTO `secrertary` (`secrertary_id`, `name`, `username`, `email`, `phone`) VALUES
(5, 'Ανθή Παππά', 'secretary1', 'anthi@gmail.com', '2610444');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `area` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile_phone` int(20) DEFAULT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `area`, `email`, `mobile_phone`, `username`) VALUES
(1, 'John Doe', 'Athens', 'john.doe@example.com', 2100, 'st1'),
(2, 'Jane Smith', 'Thessaloniki', 'jane.smith@example.com', 2102, 'st2'),
(3, 'Alice Johnson', 'Patras', 'alice.johnson@example.com', 2103, 'st3'),
(4, 'Bob Brown', 'Heraklion', 'bob.brown@example.com', 2104, 'st4'),
(5, 'Charlie Green', 'Larissa', 'charlie.green@example.com', 2105, 'st5'),
(6, 'Diana White', 'Volos', 'diana.white@example.com', 2106, 'st6'),
(7, 'Eve Black', 'Ioannina', 'eve.black@example.com', 2107, 'student7'),
(8, 'Frank Blue', 'Kavala', 'frank.blue@example.com', 2108, 'st8');

-- --------------------------------------------------------

--
-- Table structure for table `student_presentation`
--

CREATE TABLE `student_presentation` (
  `physical_presense` tinyint(1) NOT NULL,
  `meeting_room_or_link` varchar(500) NOT NULL,
  `thesis_assignment_id` int(11) NOT NULL,
  `meeting_hour` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `supervisor_announcement_presentation_path` varchar(500) DEFAULT NULL,
  `examination_protocol_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_presentation`
--

INSERT INTO `student_presentation` (`physical_presense`, `meeting_room_or_link`, `thesis_assignment_id`, `meeting_hour`, `supervisor_announcement_presentation_path`, `examination_protocol_path`) VALUES
(1, 'Γ Αίθουσα CEID', 8, '2025-04-17 14:05:39', '../../uploads/thesis_presentations/teacher3/81742979537_bdqOInyY.pdf', '../../uploads/exam_protocol/8_20250330.html'),
(1, 'Πάτρα CEID', 12, '2025-06-07 14:11:17', '../../uploads/thesis_presentations/teacher5/assignment12/1749305477_chXCZgKb.pdf', NULL),
(0, 'https://google.com', 30, '2025-06-13 16:26:00', '../../uploads/thesis_presentations/teacher1/assignment30/1749134339_sOrMGwCg.pdf', '../../uploads/exam_protocol/30_20250605.html');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `name`, `username`) VALUES
(1, 'Prof. Alpha', 'teacher1'),
(2, 'Prof. Beta', 'teacher2'),
(3, 'Prof. Gamma', 'teacher3'),
(4, 'Prof. Delta', 'teacher4'),
(5, 'Prof. Epsilon', 'teacher5'),
(6, 'Prof. Zeta', 'teacher6');

-- --------------------------------------------------------

--
-- Table structure for table `thesis_assignments`
--

CREATE TABLE `thesis_assignments` (
  `thesis_assignment_id` int(11) NOT NULL,
  `assignment_date` date NOT NULL DEFAULT curdate(),
  `status` enum('Pending','Active','Completed','Under Examination','Cancelled','Under Grading') NOT NULL,
  `student_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis_assignments`
--

INSERT INTO `thesis_assignments` (`thesis_assignment_id`, `assignment_date`, `status`, `student_id`, `topic_id`) VALUES
(6, '2025-03-04', 'Pending', 1, 1),
(7, '2025-03-04', 'Active', 2, 2),
(8, '2025-03-05', 'Completed', 3, 3),
(9, '2025-03-06', 'Completed', 4, 4),
(10, '2025-03-07', 'Cancelled', 5, 5),
(11, '2025-03-19', 'Active', 5, 7),
(12, '2025-03-23', 'Under Grading', 6, 8),
(14, '2025-03-23', 'Cancelled', 7, 9),
(15, '2025-03-24', 'Cancelled', 7, 5),
(16, '2025-04-21', 'Active', 7, 11),
(29, '2025-06-02', 'Cancelled', 8, 14),
(30, '2025-06-05', 'Completed', 8, 16);

-- --------------------------------------------------------

--
-- Table structure for table `thesis_logs`
--

CREATE TABLE `thesis_logs` (
  `thesis_log_id` int(11) NOT NULL,
  `change_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `thesis_assignment_id` int(11) NOT NULL,
  `change_log` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis_logs`
--

INSERT INTO `thesis_logs` (`thesis_log_id`, `change_timestamp`, `thesis_assignment_id`, `change_log`) VALUES
(1, '2025-03-23 15:18:06', 14, 'becoming pending'),
(2, '2025-03-23 15:44:18', 14, 'Από Pending σε Cancelled'),
(3, '2025-03-23 15:53:10', 14, 'Από Υπό Ανάθεση σε Ακυρώση'),
(4, '2025-03-24 10:04:52', 15, 'Υπό Ανάθεση'),
(5, '2025-03-29 15:57:53', 8, 'Από Υπό Εξέταση σε Ολοκληρωμένη'),
(6, '2025-03-30 07:38:31', 8, 'Από Υπό Εξέταση σε Υπό Βαθμολόγηση'),
(7, '2025-04-08 08:43:52', 15, 'Από Υπό Ανάθεση σε Ακυρώση'),
(8, '2025-04-21 13:28:21', 16, 'Υπό Ανάθεση'),
(9, '2025-04-21 13:48:43', 16, 'Η ΑΝΑΘΕΣΗ ΕΝΕΡΓΟΠΟΙΗΘΗΚΕ'),
(22, '2025-06-02 10:05:08', 29, 'Υπό Ανάθεση'),
(23, '2025-06-02 10:19:13', 29, 'Από Υπό Ανάθεση σε Ακυρώση'),
(24, '2025-06-02 10:51:15', 29, 'Η ΑΝΑΘΕΣΗ ΕΝΕΡΓΟΠΟΙΗΘΗΚΕ'),
(27, '2025-06-03 15:47:13', 29, 'ΑΚΥΡΩΣΗ ΘΕΜΑΤΟΣ ΛΟΓΩ ΦΟΙΤΗΤΗ'),
(28, '2025-06-03 15:59:21', 7, 'ΕΠΙΣΗΜΗ ΑΝΑΘΕΣΗ ΘΕΜΑΤΟΣ'),
(29, '2025-06-05 09:44:55', 30, 'Υπό Ανάθεση'),
(30, '2025-06-05 10:20:59', 30, 'Η ΑΝΑΘΕΣΗ ΕΝΕΡΓΟΠΟΙΗΘΗΚΕ'),
(31, '2025-06-05 13:35:36', 30, 'Από Ενεργό σε Υπό Εξέταση'),
(33, '2025-06-05 14:43:19', 30, 'Από Υπό Εξέταση σε Υπό Βαθμολόγηση'),
(34, '2025-06-05 15:42:19', 30, 'ΕΠΙΣΗΜΗ ΑΝΑΘΕΣΗ ΘΕΜΑΤΟΣ'),
(35, '2025-06-07 15:42:41', 30, 'ΕΠΙΣΗΜΗ ΟΛΟΚΛΗΡΩΣΗ ΘΕΜΑΤΟΣ');

-- --------------------------------------------------------

--
-- Table structure for table `thesis_material_student`
--

CREATE TABLE `thesis_material_student` (
  `thesis_material_student_id` int(11) NOT NULL,
  `thesis_assignment_id` int(11) NOT NULL,
  `thesis_draft_text_pdf_path` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis_material_student`
--

INSERT INTO `thesis_material_student` (`thesis_material_student_id`, `thesis_assignment_id`, `thesis_draft_text_pdf_path`) VALUES
(1, 8, '../../uploads/student_material/st3/thesis_draft_text_8.pdf'),
(2, 12, '../../uploads/student_material/st6/1748881568_ayLSPAJb_draft.pdf'),
(4, 30, '../../uploads/student_material/st8/1749131833_VmoukiJK_draft.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `thesis_nemertis_links`
--

CREATE TABLE `thesis_nemertis_links` (
  `thesis_assignment_id` int(11) NOT NULL,
  `nemertis_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis_nemertis_links`
--

INSERT INTO `thesis_nemertis_links` (`thesis_assignment_id`, `nemertis_link`) VALUES
(30, 'https://nemertes.library.upatras.gr/items/acb7e00c-c7f7-4efa-aa9b-792f0149625a');

-- --------------------------------------------------------

--
-- Table structure for table `thesis_topics`
--

CREATE TABLE `thesis_topics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `pdf_file_path` varchar(255) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis_topics`
--

INSERT INTO `thesis_topics` (`id`, `title`, `description`, `pdf_file_path`, `supervisor_id`) VALUES
(1, 'Alignment of 3-D Images of Biological Objects by Estimating Elastic Transformations in Image Pairs and Image Sets', 'Εδώ είναι ένα crew-rns!!', '../../uploads/thesis_topics/teacher1/d4d8038b3552b032_Use_case_v0_2 (2).pdf', 1),
(2, 'Ανάπτυξη Μοντέλου Πρακτόρων για τη Βελτιστοποίηση της Διαχείρισης Πλαστικών Αποβλήτων στην Αγροτική Παραγωγή', 'Η εργασία θα αναπτύξει ένα Agent-Based Model (ABM) για τη διαχείριση\r\nπλαστικών αποβλήτων από αγροτικές καλλιέργειες, αξιοποιώντας δεδομένα για τα είδη\r\nκαλλιεργειών, τη χρήση πλαστικών προϊόντων, και τα γεωγραφικά χαρακτηριστικά. Στόχος\r\nείναι η βελτιστοποίηση της συλλογής και ανακύκλωσης πλαστικών αποβλήτων,\r\nπροσομοιώνοντας διαφορετικά σενάρια διάρκειας ζωής πλαστικών και την εφαρμογή\r\nβιοδιασπώμενων υλικών, λαμβάνοντας υπόψη τις οικολογικές επιπτώσεις και τις\r\nκοινωνικοοικονομικές παραμέτρους. (ενδεχομένως να οδηγήσει σε δημοσίευση)\r\n', '../../uploads/thesis_topics/teacher2/1741080534_HsxNmgCo_------------.pdf', 2),
(3, ' Τεχνητά νευρωνικά δίκτυα για τη βελτίωση της ανάθεσης πόρων σε δίκτυα 5G με χρήση της τεχνολογίας MIMO', 'Χρήση τεχνητών νευρωνικών δικτύων (Artificial Neural Networks - ANNs) για τη βελτίωση της δυναμικής ανάθεσης πόρων σε δίκτυα πέμπτης γενιάς (5G), με έμφαση στην τεχνολογία Massive MIMO (Multiple Input Multiple Output). Η αυξανόμενη ζήτηση για υπηρεσίες υψηλής ταχύτητας και ποιότητας δημιουργεί την ανάγκη για αποδοτική διαχείριση πόρων, όπως το φάσμα και η ενέργεια. Τα δίκτυα 5G, σε συνδυασμό με την τεχνολογία MIMO και τις μεθόδους τεχνητής νοημοσύνης, αποτελούν τη βάση για τη βελτιστοποίηση αυτών των διαδικασιών.', '../../uploads/thesis_topics/teacher3/1741167748_oFZQvXBm_------------5G-----MIMO.pdf', 3),
(4, 'Μικροσυντονισμός (finetuning) πολυγλωσσικών μοντέλων για ταξινόμηση βιοϊατρικής πληροφορίας με μηδενική εκμάθηση (Zero-Shot Learning)', 'Η ταξινόμηση με μηδενική εκμάθηση (zero-shot classification) βασίζεται στο γεγονός ότι μοντέλα γενικής γλώσσας που έχουν βελτιστοποιηθεί στον συμπερασμό μπορούν να χρησιμοποιηθούν για ταξινόμηση χωρίς ειδική εκπαίδευση σε σύνολα δεδομένων. Μεγάλα μοντέλα γλώσσας έχουν ικανοποιητικά αποτελέσματα με πολύ λιγότερα εξειδικευμένα δεδομένα εκπαίδευσης σε σύγκριση με μικρότερα μοντέλα.Επίσης, προκαλεί έκπληξη το γεγονός ότι πολυγλωσσικά μοντέλα ξεπερνούν αντίστοιχα μονογλωσσικά ακόμα και σε μονογλωσσικές εργασίες. Η παρούσα εργασία θα διερευνήσει κατά πόσο ο μικροσυντονισμός (finetuning) ενός προεκπαιδευμένου μοντέλου μπορεί να βοηθήσει στην απόδοση και την ακρίβεια της τεχνικής μηδενικής εκμάθησης. Για τον μικροσυντονισμό μπορούν να χρησιμοποιηθούν σώματα βιοϊατρικών κειμένων ή και οι αναπαραστάσεις των κλάσεων ταξινόμησης (για παράδειγμα, οντολογία σε λεκτική περιγραφή). Απώτερος στόχος είναι να επιτευχθεί εξοικονόμηση πόρων και ταξινόμηση σε πραγματικό χρόνο.', '../../uploads/thesis_topics/teacher5/1741254921_yzmgrCch_-finetuning----------Zero-Shot-Learning.pdf', 5),
(5, 'Σχεδίαση και υλοποίηση εφαρμογής, για κινητό, για παροχή υπηρεσιών εθελοντών σε ευπαθείς ομάδες.', 'κατι δικα μου', '../../uploads/thesis_topics/teacher4/1741343709_QNcyZMaA_------------.pdf', 4),
(6, 'Δημιουργία ψηφιακού δίδυμου ευφυούς θερμοκηπίου με χρήση τεχνικών Μοντελοποίησης με βάση πράκτορες', 'Η εργασία στοχεύει στη δημιουργία ενός ψηφιακού διδύμου (digital twin) ενός\r\nέξυπνου θερμοκηπίου χρησιμοποιώντας τεχνικές Agent-Based Modeling (ABM). Το ψηφιακό\r\nδίδυμο θα προσομοιώνει την αλληλεπίδραση μεταξύ παραγόντων όπως το κλίμα, οι\r\nκαλλιέργειες, οι αισθητήρες, και τα αυτόνομα ρομποτικά συστήματα, επιτρέποντας την\r\nεξομοίωση διαφόρων σεναρίων για την καλύτερη διαχείριση των θερμοκηπίων και τη\r\nβελτιστοποίηση της παραγωγής. Το ABM θα δώσει έμφαση στη συμπεριφορά των επιμέρους\r\nπαραγόντων (agents) σε ένα έξυπνο περιβάλλον, ενώ το ψηφιακό δίδυμο θα προσφέρει τη\r\nδυνατότητα ελέγχου σε πραγματικό χρόνο. (ενδεχομένως να οδηγήσει σε δημοσίευση)\r\n', '../../uploads/thesis_topics/teacher2/1742290984_muHhYflM_-----------.pdf', 2),
(7, 'Ανάπτυξη Γραφοκεντρικού Συστήματος Ανάκτησης Πληροφοριών και Υλοποίηση Web Εφαρμογής για Αξιολόγηση Αποτελεσμάτων', 'Σε αυτή τη διπλωματική εργασία, στόχος θα είναι η ανάπτυξη ενός γραφοκεντρικού μοντέλου\r\nανάκτησης πληροφοριών που θα επεκτείνει το παραδοσιακό set-based μοντέλο, καθώς και η\r\nυλοποίηση μιας ολοκληρωμένης web εφαρμογής για την παρουσίαση και αξιολόγηση των\r\nαποτελεσμάτων αναζητήσεων. Η έρευνα θα επικεντρωθεί στην κατασκευή μιας πλατφόρμας που\r\nεπιτρέπει στους χρήστες να εκτελούν αναζητήσεις, να λαμβάνουν καταταγμένα αποτελέσματα\r\nβάσει του προτεινόμενου μοντέλου, και να αξιολογούν την απόδοση του συστήματος.\r\nΗ εργασία θα περιλαμβάνει:\r\n• Την ανάπτυξη του γραφοκεντρικού μοντέλου και την ενσωμάτωσή του σε ένα σύστημα\r\nανάκτησης πληροφοριών.\r\n• Τη δημιουργία μιας φιλικής προς τον χρήστη web εφαρμογής που θα επιτρέπει τη\r\nδιαδραστική αναζήτηση και προβολή αποτελεσμάτων.\r\n• Την υλοποίηση μηχανισμών αξιολόγησης της απόδοσης του μοντέλου μέσα από την\r\nεφαρμογή.\r\n• Την πειραματική σύγκριση του προτεινόμενου συστήματος με άλλα μοντέλα ανάκτησης\r\nπληροφοριών μέσω της web εφαρμογής.', '../../uploads/thesis_topics/teacher6/1742370720_AHNZhgSU_-------Web----.pdf', 6),
(8, 'Σημασιολογική αντιστοίχιση δεδομένων μεγάλου όγκου σε γράφους γνώσης με χρήση διανυσμάτων λέξεων', 'Σημασιολογική αντιστοίχιση δεδομένων μεγάλου όγκου σε γράφους γνώσης με χρήση διανυσμάτων λέξεων', '../../uploads/thesis_topics/teacher5/1742717570_roHAbPiG_-----------.pdf', 5),
(9, 'Σχεδιασμός δικτυακών διεπαφών για μεγάλα γλωσσικά μοντέλα (LLMs) και ανάπτυξη web εφαρμογών μηδενικής εκμάθησης.', 'Η επιτυχία των γλωσσικών μοντέλων βασίζεται μεταξύ άλλων στο γεγονός ότι τα μοντέλα αυτά πετυχαίνουν σε μεγάλο βαθμό μεταφορά της εκμάθησης (transfer learning) ενός γνωστικού πεδίου σε ένα άλλο, συναφές ή/και μη. Οι προτροπές που χρησιμοποιούνται σε αυτά οδηγούν στη δημιουργία αυθεντικών απαντήσεων με ουσιαστικά μηδενική εκμάθηση. Ενώ όμως πολλά από αυτά διαθέτουν APIs ή είναι διαθέσιμα σε δημόσια αποθετήρια δεν υπάρχει καθιερωμένος, άμεσος τρόπος για την εξ αποστάσεως προσπέλαση και προγραμματιστική αξιοποίησή τους, ώστε να παραχθούν συμπερασμοί (inference). Η διπλωματική αυτή εργασία θα εξετάσει:\r\n\r\nΤα κυριότερα διαθέσιμα LLMs, τα APIs και τα αποθετήρια, όπως το huggingface και το AzureML.\r\nΠλαίσια ανάπτυξης εφαρμογών Web βασισμένα κυρίως σε JS, όπως Next.js, node και React.\r\nΤην ανάπτυξη πιλοτικής web εφαρμογής μηδενικής εκμάθησης, για ένα πεδίο εφαρμογής, όπως η ταξινόμηση.', '../../uploads/thesis_topics/teacher5/1742739567_QFMHywAT_-------LLMs---web---.pdf', 5),
(10, 'σαδφσ σδφσδφσ', 'γφδγ\r\nφδγ\r\nδφγ\\\r\n\\\\δφγδφγ\r\n\r\nφγδφγφδ', '../../uploads/thesis_topics/teacher4/1742810453_jnvOHlUK_-.pdf', 4),
(11, 'hello', 'geia sas', '../../uploads/thesis_topics/teacher1/1742831891_NyvVBPcZ_hello.pdf', 1),
(14, 'Καλημέρα Ελλάδα', 'Γειά σας', '../../uploads/thesis_topics/teacher1/1748855990_JLeCaGtB_-.pdf', 1),
(16, 'Τίτλος Διπλωματικής CEID', 'Περιγραφή Διπλωματικής CΕΙD θα την κάνω ανάθεση στον κ. Μπαρτζώκα.', '../../uploads/thesis_topics/teacher1/1749115448_vTYABMHg_--CEID.pdf', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_det`
--

CREATE TABLE `user_det` (
  `USER` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `userType` enum('student','teacher','secrertary') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_det`
--

INSERT INTO `user_det` (`USER`, `password`, `userType`) VALUES
('secretary1', '123', 'secrertary'),
('st1', '123', 'student'),
('st2', '123', 'student'),
('st3', '123', 'student'),
('st4', '123', 'student'),
('st5', '123', 'student'),
('st6', '123', 'student'),
('st8', '123', 'student'),
('student7', '123', 'student'),
('teacher1', '123', 'teacher'),
('teacher2', '123', 'teacher'),
('teacher3', '123', 'teacher'),
('teacher4', '123', 'teacher'),
('teacher5', '123', 'teacher'),
('teacher6', '123', 'teacher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `additional_student_material`
--
ALTER TABLE `additional_student_material`
  ADD PRIMARY KEY (`add_st_material_id`),
  ADD KEY `add_st_material_constraint` (`thesis_assignment_id`);

--
-- Indexes for table `assembly_decisions`
--
ALTER TABLE `assembly_decisions`
  ADD PRIMARY KEY (`desicion_id`),
  ADD KEY `thesis_assignment_id` (`thesis_assignment_id`),
  ADD KEY `secretary` (`secretary_id`);

--
-- Indexes for table `committee`
--
ALTER TABLE `committee`
  ADD PRIMARY KEY (`com_id`),
  ADD KEY `thesis_assignment_id` (`thesis_assignment_id`);

--
-- Indexes for table `committee_invitations`
--
ALTER TABLE `committee_invitations`
  ADD PRIMARY KEY (`invitation_id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `thesis_assignment_id` (`thesis_assignment_id`);

--
-- Indexes for table `committee_members`
--
ALTER TABLE `committee_members`
  ADD PRIMARY KEY (`com_id`,`teacher_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `committee_members_ibfk3` (`mark_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`mark_id`);

--
-- Indexes for table `professor_notes`
--
ALTER TABLE `professor_notes`
  ADD PRIMARY KEY (`prof_note`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `secrertary`
--
ALTER TABLE `secrertary`
  ADD PRIMARY KEY (`secrertary_id`),
  ADD UNIQUE KEY `idx_username` (`username`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `idx_username` (`username`);

--
-- Indexes for table `student_presentation`
--
ALTER TABLE `student_presentation`
  ADD PRIMARY KEY (`thesis_assignment_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `idx_username` (`username`);

--
-- Indexes for table `thesis_assignments`
--
ALTER TABLE `thesis_assignments`
  ADD PRIMARY KEY (`thesis_assignment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `thesis_logs`
--
ALTER TABLE `thesis_logs`
  ADD PRIMARY KEY (`thesis_log_id`),
  ADD KEY `thesis_assignment_id` (`thesis_assignment_id`);

--
-- Indexes for table `thesis_material_student`
--
ALTER TABLE `thesis_material_student`
  ADD PRIMARY KEY (`thesis_material_student_id`),
  ADD KEY `thesis_assignment_id` (`thesis_assignment_id`);

--
-- Indexes for table `thesis_nemertis_links`
--
ALTER TABLE `thesis_nemertis_links`
  ADD PRIMARY KEY (`thesis_assignment_id`);

--
-- Indexes for table `thesis_topics`
--
ALTER TABLE `thesis_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `user_det`
--
ALTER TABLE `user_det`
  ADD PRIMARY KEY (`USER`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `additional_student_material`
--
ALTER TABLE `additional_student_material`
  MODIFY `add_st_material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assembly_decisions`
--
ALTER TABLE `assembly_decisions`
  MODIFY `desicion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `committee`
--
ALTER TABLE `committee`
  MODIFY `com_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `committee_invitations`
--
ALTER TABLE `committee_invitations`
  MODIFY `invitation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `mark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `professor_notes`
--
ALTER TABLE `professor_notes`
  MODIFY `prof_note` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `secrertary`
--
ALTER TABLE `secrertary`
  MODIFY `secrertary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `thesis_assignments`
--
ALTER TABLE `thesis_assignments`
  MODIFY `thesis_assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `thesis_logs`
--
ALTER TABLE `thesis_logs`
  MODIFY `thesis_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `thesis_material_student`
--
ALTER TABLE `thesis_material_student`
  MODIFY `thesis_material_student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `thesis_topics`
--
ALTER TABLE `thesis_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `additional_student_material`
--
ALTER TABLE `additional_student_material`
  ADD CONSTRAINT `add_st_material_constraint` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`);

--
-- Constraints for table `assembly_decisions`
--
ALTER TABLE `assembly_decisions`
  ADD CONSTRAINT `assembly_decisions_ibfk_1` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`),
  ADD CONSTRAINT `secretary` FOREIGN KEY (`secretary_id`) REFERENCES `secrertary` (`secrertary_id`);

--
-- Constraints for table `committee`
--
ALTER TABLE `committee`
  ADD CONSTRAINT `committee_ibfk_1` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`);

--
-- Constraints for table `committee_invitations`
--
ALTER TABLE `committee_invitations`
  ADD CONSTRAINT `committee_invitations_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `committee_invitations_ibfk_2` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`);

--
-- Constraints for table `committee_members`
--
ALTER TABLE `committee_members`
  ADD CONSTRAINT `committee_members_ibfk3` FOREIGN KEY (`mark_id`) REFERENCES `marks` (`mark_id`),
  ADD CONSTRAINT `committee_members_ibfk_1` FOREIGN KEY (`com_id`) REFERENCES `committee` (`com_id`),
  ADD CONSTRAINT `committee_members_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `professor_notes`
--
ALTER TABLE `professor_notes`
  ADD CONSTRAINT `professor_notes_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `professor_notes_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`);

--
-- Constraints for table `secrertary`
--
ALTER TABLE `secrertary`
  ADD CONSTRAINT `secrertary_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user_det` (`USER`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user_det` (`USER`);

--
-- Constraints for table `student_presentation`
--
ALTER TABLE `student_presentation`
  ADD CONSTRAINT `student_presentation_ibfk_1` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user_det` (`USER`);

--
-- Constraints for table `thesis_assignments`
--
ALTER TABLE `thesis_assignments`
  ADD CONSTRAINT `thesis_assignments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `thesis_assignments_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `thesis_topics` (`id`);

--
-- Constraints for table `thesis_logs`
--
ALTER TABLE `thesis_logs`
  ADD CONSTRAINT `thesis_logs_ibfk_1` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thesis_material_student`
--
ALTER TABLE `thesis_material_student`
  ADD CONSTRAINT `thesis_material_student_ibfk_1` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thesis_nemertis_links`
--
ALTER TABLE `thesis_nemertis_links`
  ADD CONSTRAINT `thesis_nemertis_links_ibfk_1` FOREIGN KEY (`thesis_assignment_id`) REFERENCES `thesis_assignments` (`thesis_assignment_id`);

--
-- Constraints for table `thesis_topics`
--
ALTER TABLE `thesis_topics`
  ADD CONSTRAINT `thesis_topics_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `teacher` (`teacher_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
