#include <iostream>
using namespace std;

int main() {
    double total_marks = 0;
    double highest_mark = -1;
    double lowest_mark = 101;
    int num_students = 5;

    int counter = 0;
    while (counter < num_students) {
        double mark;
        cout << "Enter the mark for student " << counter + 1 << ": ";
        cin >> mark;

        total_marks += mark;

        if (mark > highest_mark) {
            highest_mark = mark;
        }
        if (mark < lowest_mark) {
            lowest_mark = mark;
        }

        counter++;
    }

    double average_mark = total_marks / num_students;

    cout << "Average Mark: " << average_mark << endl;
    cout << "Highest Mark: " << highest_mark << endl;
    cout << "Lowest Mark: " << lowest_mark << endl;

    return 0;
}