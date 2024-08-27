<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [

        'Office of the Schools Division Superintendent' => [
            'Travel Authority',
            'Other Requests/Inquiries',
            'Feedback/Complaint'
        ],
        'Office of the Assistant Schools Division Superintendent' => [
            'Bids and Awards Committee',
            'Other Requests/Inquiries',
            'Feedback/Complaint'
        ],
        'Administration Section' => [
            'Cash, General Services, Procurement' => [
                'Cash Advance',
                'General Services-Related',
                'Procurement-Related',
                'Other Requests/Inquiries'
            ],
            'Personnel' => [
                'Application- Teaching Position',
                'Application- Non-teaching/Teaching-related',
                'Appointments (New, Promotion, Transfer, etc.)',
                'COE- Certificate of Employment',
                'Correction of Name/Change of Status',
                'ERF- Equivalent Record Form',
                'Leave Application',
                'Loan Approval and Verification',
                'Retirement',
                'Service Record',
                'Terminal Leave',
                'Other requests/inquiries'
            ],
            'Property and Supply' => [
                'Inspection/Acceptance/Distribution of LRs, Supplies, Equipment',
                'Property and Equipment Clearance',
                'Request/Issuance of Supplies',
                'Other requests/inquiries'
            ],
            'Records' => [
                'CAV- Certification, Authentication, Verification',
                'Certified True Copy (CTC)/Photocopy of Documents',
                'Non-Certified True Copy Documents',
                'Receiving and Releasing of Documents',
                'Other Requests/Inquiries',
                'Feedback/Complaint'
            ],
        ],
            'Curriculum Implementation Division' => [
                'ALS Enrolment',
                'Access to LR Portal',
                'Borrowing of books/learning materials',
                'Contextualized Learning Resources',
                'Quality Assurance of Supplementary Learning Resources',
                'Instructional Supervision',
                'Technical Assistance',
                'Other Requests/Inquiries'
        ],
            'Accounting and Budget Section' => [
                'Accounting-related',
                'ORS- Obligation Request and Status',
                'Posting/Updating of Disbursements',
                'Other Requests/Inquiries'
        ],
            'Information and Communication Technology' => [
                'Create/delete/rename/reset user accounts',
                'Troubleshooting of ICT equipment',
                'Uploading of publications',
                'Other Requests/Inquiries'
        ],
        'Legal Section' => [
            'Certificate of No Pending Case',
            'Correction of entries in School Record',
            'Legal Advice/Opinion',
            'Site Silting',
            'Feedback/Complaints'
        ],
        'School Governance and Operations Division' => [
            'Monitoring and Evaluation, Social Mobilization and Networking, Planning and Research, Human Resource Development, Physical Facilities, School Health and Nutrition Unit' => [
                'Private School-Related',
                'Basin Education Data',
                'EBEIS/LIS/NAT Data and Performance Indicators',
                'Other Requests/Inquiries',
            ],
            'Private School- Related' => [
                'Additional SHS track for private schools',
                'Increase in tuition/other school fees',
                'No increase in tuition/other school fees',
                'Private schools permit/recognition/renewal',
                'Special Orders- graduation of private schools learners',
                'Summer permit for private schools',
                'Other Private School Concerns'
            ],
        ],
        'Schools' => [
            'Enrollment',
            'Teacher I application',
            'Certified True Copy (CTC) of documents',
            'Personnel records (COE), service record, etc.',
            'Distribution of modules',
            'Borrowing of books/learning materials',
            'Inventory (school/laboratory)',
            'Learning and Development (L&D)',
            'Public Assistance (Feedback/complaints)',
            'Receiving/releasing of documents',
            'Clearance',
            'School Permanent Records',
            'Service Credits/Certification of Compensatory Time Credits',
            'Use/rental of school facilities (gym, covered court, etc.)',
            'Other Requests/Inquiries'
        ]

        ];   
    

        foreach ($offices as $officeName => $services) {
            $office = Office::create(['office_name' => $officeName]);

            $this->processServices($office, $services);
        }
    }

    private function processServices($office, $services, $prefix = '')
    {
        foreach ($services as $serviceName => $subServices) {
            if (is_array($subServices)) {
                if (is_int($serviceName)) {
                    $serviceName = $subServices;
                    $subServices = null;
                }

                $this->processServices($office, $subServices, $prefix . $serviceName . ' - ');
            } else {
                Service::create([
                    'office_id' => $office->id,
                    'service' => $prefix . $subServices
                ]);
            }
        }
    }
}

    

