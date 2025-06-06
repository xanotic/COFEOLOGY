#include <iostream>

using namespace std;

int main() {
    int totalMarks;
    cout << "Enter the total marks: ";
    cin >> totalMarks;

    // Define the grading scale
    int A_min = 90;
    int B_min = 80;
    int C_min = 70;
    int D_min = 60;

    char grade;

    // Determine the grade based on the total marks
    if (totalMarks >= A_min) {
        grade = 'A';
    } else if (totalMarks >= B_min) {
        grade = 'B';
    } else if (totalMarks >= C_min) {
        grade = 'C';
    } else if (totalMarks >= D_min) {
        grade = 'D';
    } else {
        grade = 'F';
    }

    cout << "The student's grade is: " << grade << endl;

    return 0;
}
