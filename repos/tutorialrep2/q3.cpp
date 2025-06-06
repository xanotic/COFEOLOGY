    #include <iostream>
    using namespace std;

    int main(){
        int number;
        int count = 0;

        cout << "insert a positive integer ";
        cin >> number;

        int i = 1; 

        do
        {
            if (number % i == 0) {
                cout << i << " ";
                count++;
        }
            i++;
        } while (i <= number);
        
        cout << endl << "number "<< number << " has " <<count<<" factors";

    }
    