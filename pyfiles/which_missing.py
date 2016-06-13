#!/usr/bin/env python

import sys, os
import argparse
import glob

OKGREEN = '\033[92m'
FAIL = '\033[91m'
ENDC = '\033[0m'

test_fnames = [
"2015DoubleEG_1.root",
"2015DoubleMuon_1.root",
"2015SingleEl_1.root",
"2015SingleEl_2.root",
"2015SingleEl_3.root",
"2015SingleMuon_3.root", # missing 1, 2
"2015SingleMuon_4.root",
"2015SingleMuon_5.root",
"2015SingleMuon_6.root",
"DY_madgraph_1.root", # missing 6
"DY_madgraph_2.root",
"DY_madgraph_3.root",
"DY_madgraph_4.root",
"DY_madgraph_5.root",
"DY_madgraph_7.root",
"DY_madgraph_8.root",
"DY_madgraph_9.root",
"DY_madgraph_10.root",
"DY_madgraph_11.root",
"merged_ntuple_1.root", # missing 2, 4, 5, 6, 8
"merged_ntuple_3.root",
"merged_ntuple_7.root",
"merged_ntuple_9.root",
]


def print_missing(fnames, quiet=False, themax=None):
    # key is non-numeric prefix and values are lists of the numbers
    d = {}
    for fname in fnames:
        try:
            rev = fname[::-1]
            non_num = ""
            num = ""
            started_number = False
            finished_number = False
            for c in rev:
                if c.isdigit() and not finished_number:
                    started_number = True
                    num += c
                    continue
                else:
                    if started_number and not finished_number: 
                        finished_number = True
                    if finished_number:
                        non_num += c

            num = num[::-1]
            non_num = non_num[::-1]
            if non_num not in d: d[non_num] = []
            d[non_num].append(int(num))

        except:
            pass

    for pfx in d.keys():
        try:
            tot = max(d[pfx])
            if themax: tot = int(themax)
            missing = sorted(list(set(range(1,tot+1))-set(d[pfx])))
            if len(missing) == 0: print "%s %s*.root: ALL GOOD (%i) %s" % (OKGREEN, pfx, tot, ENDC)
            else: print "%s %s*.root: MISSING %i/%i %s" % (FAIL, pfx, len(missing), tot, ENDC)

            if not quiet:
                for miss in missing: print "   missing %s%i.root" % (pfx, miss)
            print 
        except:
            pass


if __name__ == "__main__":

    # print_missing(test_fnames)

    parser = argparse.ArgumentParser()
    parser.add_argument("location", help="directory (or wildcard for files) to check")
    parser.add_argument("-q", "--quiet", help="don't show all filenames", action="store_true")
    parser.add_argument("-m", "--max", help="if you know the max number, then you can specify it")
    args = parser.parse_args()

    if "*" in args.location: fnames = glob.glob(args.location)
    else: fnames = os.listdir(args.location)

    print_missing(fnames, args.quiet, args.max)
