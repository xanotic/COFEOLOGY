#include <iostream>
using namespace std;

int main()
{
    float testscore1, testscore2, testscore3, testscore4, testscore5, ave;
    string namestu, namehigh, namelow;
    int high = 0, low = 100, i = 0, aveclass
    
    while ( i <= 45 )
    {
          cout << " Please enter the student name : " ;
          getline (cin, namestu);
          
          cout << " PLease enter 5 test score for the tudent " ;
          cin >> testscore1;
          cin >> testscore2;
          cin >> testscore3;
          cin >> testscore4;
          cin >> testscore5;
          
          ave = (testscore1 + testscore2 + testscore3 + testscore4 + testscore5 ) / 5;
          
          if ( ave > high )
          {
               high = ave ;
			   namehigh = namestu; }
          if ( ave < low )
          {
               low = ave ;
			   namelow = namestu; }
          
          i = i + 1;
    }
    
    cout << " " << endl ;
    cout << " " << endl ;
    cout << " " << endl ;
    cout << " PERFORMANCE OF FORM 5CS FOR COMPUTER SCIENCE SUBJECT " << endl;
    cout << "     The student with the highest average score of " << high << "" << namehigh << endl ;
    cout << "     The student with the lowest average score of " << low << "" << namelow << endl ;
}