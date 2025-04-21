<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Brief;
use App\Models\Submission;
use App\Models\BriefCriteria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo teacher if not exists
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'username' => 'teacherdemo',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'password' => 'password123',
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );
        
        // Create 10 students
        $students = [
            ['email' => 'student1@example.com', 'username' => 'studentone', 'first_name' => 'Alice', 'last_name' => 'Johnson'],
            ['email' => 'student2@example.com', 'username' => 'studenttwo', 'first_name' => 'Bob', 'last_name' => 'Williams'],
            ['email' => 'student3@example.com', 'username' => 'studentthree', 'first_name' => 'Claire', 'last_name' => 'Davis'],
            ['email' => 'student4@example.com', 'username' => 'studentfour', 'first_name' => 'David', 'last_name' => 'Miller'],
            ['email' => 'student5@example.com', 'username' => 'studentfive', 'first_name' => 'Emma', 'last_name' => 'Wilson'],
            ['email' => 'student6@example.com', 'username' => 'studentsix', 'first_name' => 'Frank', 'last_name' => 'Moore'],
            ['email' => 'student7@example.com', 'username' => 'studentseven', 'first_name' => 'Grace', 'last_name' => 'Taylor'],
            ['email' => 'student8@example.com', 'username' => 'studenteight', 'first_name' => 'Henry', 'last_name' => 'Anderson'],
            ['email' => 'student9@example.com', 'username' => 'studentnine', 'first_name' => 'Ivy', 'last_name' => 'Thomas'],
            ['email' => 'student10@example.com', 'username' => 'studentten', 'first_name' => 'Jack', 'last_name' => 'Jackson'],
        ];
        
        $standardPassword = 'password123';
        
        foreach ($students as $studentData) {
            User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'username' => $studentData['username'],
                    'first_name' => $studentData['first_name'],
                    'last_name' => $studentData['last_name'],
                    'password' => $standardPassword,
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]
            );
        }
        
        // Create briefs
        $briefs = [
            [
                'title' => 'Web Development Project',
                'description' => 'Create a responsive website using HTML, CSS, and JavaScript.',
                'status' => 'published',
                'deadline' => Carbon::now()->addDays(7),
                'criteria' => [
                    ['title' => 'Responsive Design', 'description' => 'The website should be fully responsive on all devices', 'order' => 1],
                    ['title' => 'Code Quality', 'description' => 'Clean, well-organized, and commented code', 'order' => 2],
                    ['title' => 'Functionality', 'description' => 'All features should work as expected', 'order' => 3],
                ]
            ],
            [
                'title' => 'Database Design Project',
                'description' => 'Design and implement a relational database for a school management system.',
                'status' => 'published',
                'deadline' => Carbon::now()->addDays(14),
                'criteria' => [
                    ['title' => 'Entity Relationship Diagram', 'description' => 'Clear ERD showing all relationships', 'order' => 1],
                    ['title' => 'Normalization', 'description' => 'Database should be normalized to at least 3NF', 'order' => 2],
                    ['title' => 'Query Performance', 'description' => 'Efficient queries with proper indexing', 'order' => 3],
                ]
            ],
            [
                'title' => 'Mobile App Design',
                'description' => 'Create wireframes and mockups for a mobile application.',
                'status' => 'published',
                'deadline' => Carbon::now()->addDays(10),
                'criteria' => [
                    ['title' => 'User Experience', 'description' => 'Intuitive and user-friendly design', 'order' => 1],
                    ['title' => 'Visual Appeal', 'description' => 'Aesthetically pleasing and consistent design', 'order' => 2],
                    ['title' => 'Accessibility', 'description' => 'Design considers accessibility guidelines', 'order' => 3],
                ]
            ],
        ];
        
        foreach ($briefs as $briefData) {
            $criteria = $briefData['criteria'];
            unset($briefData['criteria']);
            
            $brief = Brief::create(array_merge($briefData, [
                'teacher_id' => $teacher->id
            ]));
            
            // Add criteria to the brief
            foreach ($criteria as $criterionData) {
                BriefCriteria::create(array_merge($criterionData, [
                    'brief_id' => $brief->id
                ]));
            }
            
            // Create random submissions for each brief
            $studentUsers = User::where('role', 'student')->get();
            $submittingStudents = $studentUsers->random(rand(5, 8)); // Random 5-8 students submit
            
            foreach ($submittingStudents as $student) {
                Submission::create([
                    'brief_id' => $brief->id,
                    'student_id' => $student->id,
                    'content' => "Submission from " . $student->username . " for " . $brief->title . ".\n\nThis is a sample submission with placeholder content for demonstration purposes. In a real scenario, this would include detailed work related to the brief requirements.",
                    'submission_date' => Carbon::now()->subDays(rand(1, 3)),
                    'status' => 'submitted'
                ]);
            }
        }
        
        $this->command->info('Demo data has been seeded: 1 teacher, 10 students, 3 briefs with submissions');
        $this->command->info('All users have the password: ' . $standardPassword);
    }
} 