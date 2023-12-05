export class pupilsAddFilter {
  private school_id: string;
  private name: string;
  private year_group: number;
  private gender: string;
  private enrolment_date: Date;
  private house: string;
  private tutor: string;
  private boarding_status:string;
  private sen_gt_eal:string;

  constructor(
    school_id: string,
    name: string,
    year_group: number,
    gender: string,
    enrolment_date: Date,
    house: string,
    tutor: string,
    boarding_status: string,
    sen_gt_eal: string,
    ) {
      this.school_id = school_id;
      this.name = name;
      this.year_group = year_group;
      this.gender = gender;
      this.enrolment_date = enrolment_date;
      this.house = house;
      this.tutor = tutor;
      this.boarding_status = boarding_status;
      this.sen_gt_eal = sen_gt_eal;
  }
}