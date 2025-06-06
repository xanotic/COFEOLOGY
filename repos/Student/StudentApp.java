class StudentApp {
    public static void main(String[] args) {

      Student Student1 = new Student();
      Student Student2 = new Student("Anas", 'M', 64);

      Student1.displayStudent();
      Student2.displayStudent();

      Student1.setName("Amirah");
      Student1.setGender('F');
      Student1.setAge(23);

       int n = Student1.getAge();
      System.out.println(n);


    }
  }